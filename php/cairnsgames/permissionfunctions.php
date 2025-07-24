<?php

include_once __DIR__ . "/dbutils.php";
include_once __DIR__ . "/../utils.php";

$appid = getAppId();
$permissions;
$app_properties;

function getPermissions($id)
{
    global $appid, $permissions;
    $sql = "SELECT name, IF(never>0,'NEVER', if(yes>0,'YES', 'NO')) permission FROM (
        SELECT name, SUM(yes) yes, SUM(NO) no, SUM(NEVER) never FROM (
        SELECT 'Application' role, NAME, if(VALUE=1,1,0) yes, if(VALUE=0,1,0) no, if(VALUE=-1,1,0) never FROM permission
        WHERE app_id = ?
        UNION
        SELECT r.name role, p.name name, if(rp.VALUE=1,1,0) yes, if(rp.VALUE=0,1,0) no, if(rp.VALUE=-1,1,0) NEVER
            FROM permission p, role_permissions rp, role r, user_role ur
        WHERE p.app_id = ?
            AND rp.permission_id = p.id
            AND rp.role_id = r.id
            AND ur.user_id = ?
            AND ur.role_id = r.id
        UNION
        SELECT 'User' role, p.name name, if(up.VALUE=1,1,0) yes, if(up.VALUE=0,1,0) no, if(up.VALUE=-1,1,0) NEVER 
            FROM permission p, user_permissions up
        WHERE p.app_id = ?
            AND up.permission_id = p.id
            AND up.user_id = ?
            ) t
        GROUP BY NAME) t2";
    $params = [$appid, $appid, $id, $appid, $id];
    $sss = "sssss";
    $permissions = PrepareExecSQL($sql, $sss, $params);
    // var_dump($permissions);
}

function hasAccess($id, $permission)
{
    global $permissions;
    if (!isset($permissions)) {
        getPermissions($id);
    }
    // var_dump($permissions);
    foreach ($permissions as $p) {
        if ($p["name"] == $permission) {
            return $p["permission"] == "YES";
        }
    }
    return false;
}

function getSecretWithAppId($appid, $secretname, $default, $domain = null)
{
    global $debugValues;
    if ($appid == null) {
        return null;
    }
    if ($domain !== null) {
        $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND domain = ?";
        $params = [$appid, $secretname, $domain];
        $sss = "sss";
        $result = PrepareExecSQL($sql, $sss, $params);
        if (!empty($result)) {
            return $result[0]["value"];
        }
        // fallback to no domain
        $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND (domain IS NULL OR domain = '')";
        $params = [$appid, $secretname];
        $sss = "ss";
        $result = PrepareExecSQL($sql, $sss, $params);
        if (!empty($result)) {
            return $result[0]["value"];
        }
        return $default;
    } else {
        $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND (domain IS NULL OR domain = '')";
        $params = [$appid, $secretname];
        $sss = "ss";
        $result = PrepareExecSQL($sql, $sss, $params);
        if (!empty($result)) {
            return $result[0]["value"];
        }
        return $default;
    }
}
function getSecret($secretname, $default, $domain = null)
{
    global $debugValues;
    $appid = getAppId();
    return getSecretWithAppId($appid, $secretname, $default, $domain);
    // if ($appid == null) {
    //     return null;
    // }
    // if ($domain !== null) {
    //     $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND domain = ?";
    //     $params = [$appid, $secretname, $domain];
    //     $sss = "sss";
    //     $result = PrepareExecSQL($sql, $sss, $params);
    //     if (!empty($result)) {
    //         return $result[0]["value"];
    //     }
    //     // fallback to no domain
    //     $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND (domain IS NULL OR domain = '')";
    //     $params = [$appid, $secretname];
    //     $sss = "ss";
    //     $result = PrepareExecSQL($sql, $sss, $params);
    //     if (!empty($result)) {
    //         return $result[0]["value"];
    //     }
    //     return $default;
    // } else {
    //     $sql = "SELECT value FROM application_secret WHERE app_id = ? AND name = ? AND (domain IS NULL OR domain = '')";
    //     $params = [$appid, $secretname];
    //     $sss = "ss";
    //     $result = PrepareExecSQL($sql, $sss, $params);
    //     if (!empty($result)) {
    //         return $result[0]["value"];
    //     }
    //     return $default;
    // }
}

$app_properties = array();

function getProperty($name, $default, $domain = null)
{
    global $debugValues, $app_properties;
    $appid = getAppId();
    if ($appid == null) {
        return null;
    }
    if ($domain !== null) {
        $sql = "SELECT name, value FROM application_property WHERE app_id = ? AND domain = ?";
        $params = [$appid, $domain];
        $sss = "ss";
        $result = PrepareExecSQL($sql, $sss, $params);
        foreach ($result as $r) {
            $app_properties[$r["name"] . "|" . $domain] = $r["value"];
        }
        if (isset($app_properties[$name . "|" . $domain])) {
            return $app_properties[$name . "|" . $domain];
        }
        // fallback to no domain
        $sql = "SELECT name, value FROM application_property WHERE app_id = ? AND (domain IS NULL OR domain = '')";
        $params = [$appid];
        $sss = "s";
        $result = PrepareExecSQL($sql, $sss, $params);
        foreach ($result as $r) {
            $app_properties[$r["name"]] = $r["value"];
        }
        if (isset($app_properties[$name])) {
            return $app_properties[$name];
        }
        return $default;
    } else {
        if (empty($app_properties)) {
            $sql = "SELECT name, value FROM application_property WHERE app_id = ? AND (domain IS NULL OR domain = '')";
            $params = [$appid];
            $sss = "s";
            $result = PrepareExecSQL($sql, $sss, $params);
            foreach ($result as $r) {
                $app_properties[$r["name"]] = $r["value"];
            }
        }
        if (isset($app_properties[$name])) {
            return $app_properties[$name];
        }
        return $default;
    }
}

function getSecretOrProperty($name, $default, $domain = null)
{
    $secret = getSecret($name, null);
    if ($secret !== null) {
        return $secret;
    }
    return getProperty($name, $default);
}

function getUserFromToken($token)
{
    if (validateJwt($token)) {
        $data = get_jwt_payload($token)->data;
        return $data;
    }
    return null;
}
