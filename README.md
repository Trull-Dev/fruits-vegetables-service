# 🍎 Fruits and Vegetables 🥕
A Symfony-based service that manages fruits and vegetables inventory through a REST API.

## Features

- **Fruits and Vegetables Management**: Add, list, and remove fruits/vegetables with their quantities and units
- **Support for Different Units**: Handles various measurement units (grams, kilograms)
- **JSON Request/Response**: API communicates using JSON format

## Getting Started

### Prerequisites

- PHP 8.4 or higher
- Composer
- SQLite

### Setup and Testing

The project includes a Makefile with the following commands:

- `make install` - Install dependencies
- `make migrate` - Run database migrations  
- `make test` - Run all tests (excluding integration tests)
- `make test-integration` - Run integration tests only (code flow with the request.json)
- `make all` - Run full setup (install, migrate, and test)

## API Endpoints

### Fruits

- `GET /api/fruits` - List all fruits without filters
- `GET /api/fruits?name=apple&min=100&max=500&unit=kg` - List all fruits with filters
- `POST /api/fruits` - Add new fruits
- `DELETE /api/fruits/{name}` - Remove a specific fruit

### Vegetables

- `GET /api/vegetables` - List all vegetables without filters
- `GET /api/vegetables?name=carrot&min=100&max=500&unit=kg` - List all vegetables with filters
- `POST /api/vegetables` - Add new vegetables
- `DELETE /api/vegetables/{name}` - Remove a specific vegetable

### Request Format Example
```json 
[
  {
    "id": 1,
    "name": "Carrot",
    "type": "vegetable",
    "quantity": 10922,
    "unit": "g"
  },
  {
    "id": 2,
    "name": "Apples",
    "type": "fruit",
    "quantity": 20,
    "unit": "kg"
  }
]
```

