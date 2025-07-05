<?php

function printRequestFields(array $fields) {
    echo "{\n";
    foreach ($fields as $key => $val) {
        // $val can be an array or string describing the type or just a field name
        // For simplicity, treat all as string type; optional fields end with '?'
        $optional = false;
        $fieldName = $key;
        if (is_int($key)) {
            // Numeric keys: $val is field name string
            $fieldName = $val;
            $type = "string";
        } else {
            // Associative keys: key = fieldName, value = type or array
            $type = is_string($val) ? $val : "string";
        }
        if (substr($fieldName, -1) === '?') {
            $optional = true;
            $fieldName = rtrim($fieldName, '?');
        }
        echo "  $fieldName" . ($optional ? "?: " : ": ") . "$type;\n";
    }
    echo "}\n\n";
}

function generateApiDoc(array $configs, string $baseUrl) {
    echo "API base: $baseUrl<br/>\n";
    echo "For every api call send the app_id as a header value<br/>\n";
    echo "Use these exact endpoints with their documented methods and payloads:<br/>\n<br/>\n";

    foreach ($configs as $endpoint => $config) {
        if ($endpoint === 'post') {
            foreach ($config as $postEndpoint => $functionName) {
                echo "<br/>\n" . ucfirst($postEndpoint) . ": POST " . $baseUrl . "api.php/$postEndpoint<br/>\n";
                echo "Body fields:<br/>\n";
                if (isset($config['create']) && is_array($config['create'])) {
                    foreach ($config['create'] as $field) {
                        echo "  $field<br/>\n";
                    }
                } else {
                    echo "{<br/>\n  // fields depend on implementation<br/>\n}<br/>\n<br/>\n";
                }
            }
            continue;
        }

        if ($endpoint === '$$' || $endpoint === 'openapi') {
            continue;
        }

        $endpointUrl = $baseUrl . "api.php/$endpoint";

        if (isset($config['create']) && is_array($config['create'])) {
            echo "<br/>\n" . ucfirst($endpoint) . ": POST $endpointUrl<br/>\n";
            echo "Body fields:<br/>\n";
            foreach ($config['create'] as $field) {
                echo "  $field<br/>\n";
            }
        }

        if (isset($config['update']) && is_array($config['update'])) {
            $putUrl = $baseUrl . "api.php/$endpoint/:id";
            echo "<br/>\n" . ucfirst($endpoint) . " Update: PUT $putUrl<br/>\n";
            echo "Body fields:<br/>\n";
            foreach ($config['update'] as $field) {
                echo "  $field<br/>\n";
            }
        }

        if (isset($config['select'])) {
            $getAllUrl = $baseUrl . "api.php/$endpoint";
            $getSingleUrl = $baseUrl . "api.php/$endpoint/:id";
            echo "<br/>\n" . ucfirst($endpoint) . ": GET $getAllUrl<br/>\n";
            echo "Response fields:<br/>\n";
            if (is_array($config['select'])) {
                foreach ($config['select'] as $field) {
                    echo "  $field<br/>\n";
                }
            } else {
                preg_match_all('/\b(\w+)\b(?=,|\s+FROM)/', $config['select'], $matches);
                foreach ($matches[1] as $field) {
                    echo "  $field<br/>\n";
                }
            }
            echo "<br/>\n" . ucfirst($endpoint) . " Single: GET $getSingleUrl<br/>\n";
            echo "Response fields:<br/>\n";
            if (is_array($config['select'])) {
                foreach ($config['select'] as $field) {
                    echo "  $field<br/>\n";
                }
            } else {
                preg_match_all('/\b(\w+)\b(?=,|\s+FROM)/', $config['select'], $matches);
                foreach ($matches[1] as $field) {
                    echo "  $field<br/>\n";
                }
            }
        }

        if (isset($config['subkeys']) && is_array($config['subkeys'])) {
            foreach ($config['subkeys'] as $subkey => $subConfig) {
                $subkeyUrl = $baseUrl . "api.php/$endpoint/:id/$subkey";
                echo "<br/>\n" . ucfirst($endpoint) . " Subkey ($subkey): GET $subkeyUrl<br/>\n";
                echo "Response fields:<br/>\n";
                if (isset($subConfig['select'])) {
                    if (is_array($subConfig['select'])) {
                        foreach ($subConfig['select'] as $field) {
                            echo "  $field<br/>\n";
                        }
                    } else {
                        preg_match_all('/\b(\w+)\b(?=,|\s+FROM)/', $subConfig['select'], $matches);
                        foreach ($matches[1] as $field) {
                            echo "  $field<br/>\n";
                        }
                    }
                }
            }
        }
    }
}

