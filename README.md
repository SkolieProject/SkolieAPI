# SkolieAPI

## General information

| Route                 | Method | Does                                         |
| --------------------- | ------ | -------------------------------------------- |
| `/login/`             | `GET`  | Login the user                               |
| `/assay/`             | `POST` | Creates a new assay                          |
| `/assay/{assay_id}`   | `GET`  | Searches for specific assay complete content |
| `/assay/{assay_id}`   | `POST` | Rewrites questions of a specific assay       |
| `/assayGroup?{class}` | `GET`  | Gets specified assays headers                |

## `/login/`

### Return values

- JWT in Authorization header
- Body of response

### Example request

`/login/` `METHOD: GET`
username: email@email.complete
password: p4ssw0rd

### Example response

`JSON`

```

{
  "warning": "OK",
  "status message": "You have loged in sucessfully",
  "userinfo": {
    "name": "Rosana Paiolo",
    "subject": "MATM",
    "classes": [
      "EM3A",
      "EM3B"
    ]
  }
}

```
