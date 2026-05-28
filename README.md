# KasiConnect
KasiConnect is a local marketplace web application where users can list products, browse items, leave reviews, place orders, and manage seller order statuses.

This project started as a PHP/XAMPP application and was modernized by adding an ASP.NET Core Web API backend that connects to the same MySQL database.

## Tech Stack

- PHP
- JavaScript
- HTML/CSS
- ASP.NET Core Web API
- Entity Framework Core
- MySQL / MariaDB
- XAMPP
- Swagger

## Features

- User login and registration
- Browse marketplace products
- Search products
- View product details
- Add product listings
- Delete own listings
- Submit product reviews
- View product reviews
- Place orders
- View buyer order history
- Seller dashboard
- Update order status

## API Features
- `GET /api/products`
- `GET /api/products?search=value`
- `GET /api/products/{id}`
- `POST /api/products`
- `DELETE /api/products/{id}`
- `GET /api/products/{id}/reviews`
- `POST /api/products/{id}/reviews`
- `POST /api/orders`
- `GET /api/users/{userId}/orders`
- `GET /api/sellers/{sellerId}/orders`
- `PATCH /api/orders/{id}/status`

## Project Structure
```text
KasiConnect
├── CSS
├── Images
├── Includes
├── JS
├── Pages
├── KasiConnect.Api
├── index.php
└── logout.php
```

## What I Learned
- Connected an ASP.NET Core API to an existing MySQL database
- Built REST endpoints using controllers and DTOs
- Used JAvaScript `fetch()` to connect php pages to a C# backend
- Gradually modernized an existing PHP project without restarting from scratch
- Practiced backend development, API design, and database integration

## Future Improvements
- Add JWT authentication to the ASP.NET Core API
- Move image upload fully into the C# API
- Add Docker support
- Deploy the API to Azure
- Add GitHub Actions for CI/CD
- Improve frontend validation adn error handling 