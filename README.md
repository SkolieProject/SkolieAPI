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

- Headers: Authorization
  username:
  password:

- Returns JWT
