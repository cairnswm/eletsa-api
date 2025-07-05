# Upload API

The `upload.php` script is a RESTful API endpoint for uploading images in Base64 format. It processes the uploaded images, saves them to the server, and returns a JSON response with the upload results.

## Endpoint

`POST /api/upload.php`

## Request Headers

- `Content-Type: application/json`

## Request Body

The request body should be a JSON object with the following structure:

```json
{
  "userId": "<numeric_user_id>",
  "images": [
    "<base64_encoded_image_1>",
    "<base64_encoded_image_2>",
    "..."
  ]
}
```

### Parameters

- `userId` (required): Numeric ID of the user uploading the images.
- `images` (required): An array of Base64-encoded image strings.

## Response

The response is a JSON object with the following structure:

```json
{
  "success": true,
  "files": [
    {
      "name": "<file_name>",
      "size": <file_size_in_bytes>,
      "url": "<file_url>"
    }
  ],
  "errors": [
    "<error_message_1>",
    "<error_message_2>",
    "..."
  ]
}
```

### Fields

- `success`: Boolean indicating whether any files were successfully uploaded.
- `files`: An array of objects representing successfully uploaded files. Each object contains:
  - `name`: The name of the uploaded file.
  - `size`: The size of the uploaded file in bytes.
  - `url`: The URL to access the uploaded file.
- `errors`: An array of error messages for any failed uploads.

## Example Request

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{
    "userId": "123",
    "images": [
      "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...",
      "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQE..."
    ]
  }' \
  http://yourdomain.com/api/upload.php
```

## Example Response

```json
{
  "success": true,
  "files": [
    {
      "name": "123-img_60b8c2d8e4b0a.png",
      "size": 1024,
      "url": "/uploads/123/123-img_60b8c2d8e4b0a.png"
    }
  ],
  "errors": []
}
```

## Error Handling

The `errors` array in the response contains messages for any issues encountered during the upload process. Common errors include:

- `Invalid input JSON. Ensure userId and images (base 64 array) are provided.`
- `Invalid userId it is expected to be numeric.`
- `<key>: Invalid image format. Received: <format>`
- `<key>: Failed to decode base64.`
- `<key>: Failed to save file.`

## Directory Structure

Uploaded files are saved in the `uploads/<userId>/` directory relative to the project root. If the directory does not exist, it will be created automatically with `0755` permissions.
