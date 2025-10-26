# 🌱 GreenCart - Grocery Management System# 🌿 GreenCart - Professional Grocery Management SystemGreenCart — Grocery Management System (Educational demo)



A comprehensive PHP + MySQL application demonstrating **EVERY major SQL operation** with transparent SQL code display on hover and toggle features.



## 📋 Table of ContentsA comprehensive web-based grocery management system demonstrating **every major SQL operation** through an interactive admin panel with **visible SQL queries**.Overview

- [Features](#features)

- [Technologies](#technologies)--------

- [Installation](#installation)

- [File Structure](#file-structure)![Database Systems Project](https://img.shields.io/badge/Course-Database_Systems-blue)This is a small PHP + MySQL (PDO) application designed to demonstrate core SQL operations taught in a Database Systems course. It includes tables for Customers, Vendors, Products, Orders, Order_Details, and Delivery, plus a demo page that showcases JOINs, subqueries, UNION, views, pattern matching, aggregates, grouping, and simple constraint management.

- [SQL Operations Demonstrated](#sql-operations-demonstrated)

- [How to Use](#how-to-use)![PHP](https://img.shields.io/badge/PHP-7.4+-purple)

- [Database Schema](#database-schema)

![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)Quick start (Windows, XAMPP)

---

![Tailwind](https://img.shields.io/badge/Tailwind-CSS-cyan)--------------------------------

## ✨ Features

1. Place the project folder in your XAMPP `htdocs` (already placed in this workspace).

### 🎯 Educational SQL Platform

- **40+ SQL Operations** demonstrated interactively## 📋 Project Overview2. Start Apache and MySQL via the XAMPP control panel.

- **Hover Tooltips**: See exact SQL for every Add/Edit/Delete operation

- **Toggle SQL Display**: View/hide SQL code on all pages3. Open in browser: http://localhost/Database_Project_GreenCart/init_db.php to create the database and sample data.

- **Real-time Examples**: All demos use actual database data

- **Comprehensive Coverage**: JOINs, Subqueries, Aggregations, Window Functions, CTEs, and more**GreenCart** is a full-featured grocery management platform built for Database Systems coursework. It showcases professional database design with **SQL query visualization** - hover over any data to see the SQL that generated it!4. Visit http://localhost/Database_Project_GreenCart/index.php to use the dashboard.



### 💼 Professional Admin Panel

- Clean, modern UI using Tailwind CSS

- Responsive design for all screen sizes### 🎯 Key FeaturesFiles added

- Icon-based navigation (Font Awesome)

- Active page highlighting-----------

- CRUD operations for all entities

✅ **Complete SQL Coverage** - All major SQL operations demonstrated  - `db.php` — PDO connection helper. Update DB credentials if needed.

### 🔍 Transparent SQL Code

- SQL visible on hover (non-intrusive tooltips)✅ **Visual SQL Display** - Hover tooltips show queries for every operation  - `init_db.php` — Creates database, tables, sample data. Reset via `?reset=1`.

- Toggle SQL code boxes on all list pages

- Copy-to-clipboard functionality✅ **Professional UI** - Modern admin panel with Tailwind CSS  - `index.php` — Main dashboard.

- Syntax-highlighted code blocks

✅ **Interactive Learning** - Copy SQL queries, see results instantly  - `css/style.css` — Styling.

---

✅ **Working System** - Full CRUD operations for real grocery management  - `customers_*` — Full CRUD for Customers (list, form, delete).

## 🛠️ Technologies

- `vendors_list.php`, `products_list.php`, `orders_list.php`, `order_details_list.php`, `delivery_list.php` — List pages (examples, easily extended to full CRUD).

- **Backend**: PHP 8.2+ with PDO (prepared statements)

- **Database**: MySQL 5.7+ / MariaDB## 🗄️ Database Schema- `sql_ops.php` — SQL operations demo page with examples and explanations.

- **Frontend**: HTML5, Tailwind CSS (CDN)

- **Icons**: Font Awesome 6.4- `sql_ops_actions.php` — Executes DDL for constraint demo (drop/create foreign keys).

- **Server**: Apache (XAMPP)

### Tables (7)

---

Notes

## 📦 Installation

```-----

### Prerequisites

- XAMPP (or any PHP + MySQL stack)users          - System authentication- The app uses a database named `greencart_db` by default; change `db.php` constants if you prefer.

- PHP 8.2 or higher

- MySQL 5.7 or highercustomers      - Customer information  - DDL operations in `sql_ops_actions.php` are for educational purposes. Use with care.



### Setup Stepsvendors        - Supplier details- The project focuses on clarity over completeness. If you want full CRUD for every table or extra sample data, tell me and I will add them.



1. **Clone/Download** this project to your htdocs folder:products       - Product catalog with inventory

   ```orders         - Customer orders

   c:\xampp\htdocs\greencart\order_details  - Line items per order

   ```delivery       - Delivery tracking

```

2. **Start XAMPP**:

   - Start Apache### Relationships

   - Start MySQL

```

3. **Initialize Database**:customers (1) ──── (N) orders (1) ──── (1) delivery

   - Visit: `http://localhost/greencart/init_db.php`                      |

   - Or reset with fresh data: `http://localhost/greencart/init_db.php?reset=1`                    (N) order_details (N) ──── (1) products (N) ──── (1) vendors

```

4. **Access the Application**:

   - Dashboard: `http://localhost/greencart/`## ✨ SQL Features Demonstrated (40+)

   - SQL Demos: `http://localhost/greencart/sql_ops.php`

### DDL Operations

---✅ CREATE DATABASE, TABLE, VIEW, INDEX, TRIGGER, PROCEDURE  

✅ DROP TABLE, VIEW  

## 📁 File Structure✅ ALTER TABLE  



### Core Files### Constraints

| File | Purpose | SQL Operations |✅ PRIMARY KEY, FOREIGN KEY (CASCADE/RESTRICT), UNIQUE  

|------|---------|----------------|✅ CHECK, NOT NULL, DEFAULT  

| `db.php` | Database connection & PDO setup | Connection management |

| `nav.php` | Navigation menu with active highlighting | N/A |### Joins (6 types)

| `index.php` | Main dashboard with statistics | SELECT with JOINs, COUNT, SUM |✅ INNER JOIN - Matching rows only  

| `init_db.php` | Database initialization & sample data | CREATE TABLE, INSERT, CREATE VIEW |✅ LEFT JOIN - All left + matching right  

✅ RIGHT JOIN - All right + matching left  

### Entity Management (CRUD)✅ CROSS JOIN - Cartesian product  

✅ Self JOIN - Table joined to itself  

#### Customers✅ Multi-table joins (3+ tables)  

| File | SQL Operation |

|------|---------------|### Subqueries (7 types)

| `customers_list.php` | `SELECT * FROM Customers ORDER BY customer_id DESC` |✅ Scalar - Single value  

| `customers_form.php` | `INSERT INTO Customers (name, email, phone, address, preferences) VALUES (?, ?, ?, ?, ?)` |✅ Row - Single row  

| `customers_form.php` (edit) | `UPDATE Customers SET name=?, email=?, phone=?, address=?, preferences=? WHERE customer_id=?` |✅ Table - Multiple rows  

| `customers_delete.php` | `DELETE FROM Customers WHERE customer_id=?` |✅ Correlated - References outer query  

✅ EXISTS / NOT EXISTS  

#### Products✅ IN / NOT IN  

| File | SQL Operation |

|------|---------------|### Set Operations

| `products_list.php` | `SELECT p.*, v.vendor_name FROM Products p LEFT JOIN Vendors v ON p.vendor_id=v.vendor_id ORDER BY product_id DESC` |✅ UNION - Combine distinct  

| `products_form.php` | `INSERT INTO Products (vendor_id, name, category, price, sustainability_tag) VALUES (?, ?, ?, ?, ?)` |✅ UNION ALL - Combine all  

| `products_form.php` (edit) | `UPDATE Products SET vendor_id=?, name=?, category=?, price=?, sustainability_tag=? WHERE product_id=?` |✅ INTERSECT (emulated)  

| `products_delete.php` | `DELETE FROM Products WHERE product_id=?` |✅ EXCEPT (emulated)  



#### Vendors### Aggregation & Grouping

| File | SQL Operation |✅ COUNT, SUM, AVG, MAX, MIN  

|------|---------------|✅ GROUP BY (single/multiple columns)  

| `vendors_list.php` | `SELECT * FROM Vendors ORDER BY vendor_id DESC` |✅ HAVING - Group filtering  

| `vendors_form.php` | `INSERT INTO Vendors (vendor_name, contact_email, phone, location) VALUES (?, ?, ?, ?)` |✅ COALESCE - NULL handling  

| `vendors_form.php` (edit) | `UPDATE Vendors SET vendor_name=?, contact_email=?, phone=?, location=? WHERE vendor_id=?` |

| `vendors_delete.php` | `DELETE FROM Vendors WHERE vendor_id=?` |### Advanced Features

✅ Window Functions (ROW_NUMBER, RANK, DENSE_RANK)  

#### Orders✅ CASE WHEN - Conditional logic  

| File | SQL Operation |✅ Pattern Matching (LIKE, REGEXP)  

|------|---------------|✅ Triggers (BEFORE/AFTER INSERT/UPDATE)  

| `orders_list.php` | `SELECT o.*, c.name AS customer_name FROM Orders o LEFT JOIN Customers c ON o.customer_id=c.customer_id ORDER BY order_id DESC` |✅ Stored Procedures with parameters  

| `orders_form.php` | `INSERT INTO Orders (customer_id, order_date, total_amount, status) VALUES (?, NOW(), ?, ?)` |✅ Views (4 pre-built)  

| `orders_form.php` (edit) | `UPDATE Orders SET customer_id=?, total_amount=?, status=? WHERE order_id=?` |✅ Transactions (BEGIN, COMMIT, ROLLBACK)  

| `orders_delete.php` | `DELETE FROM Orders WHERE order_id=?` |

## 🚀 Quick Start

#### Order Details

| File | SQL Operation |### Prerequisites

|------|---------------|- XAMPP (Apache + MySQL + PHP)

| `order_details_list.php` | `SELECT od.*, p.name AS product_name, o.customer_id FROM Order_Details od LEFT JOIN Products p ON od.product_id=p.product_id LEFT JOIN Orders o ON od.order_id=o.order_id ORDER BY order_detail_id DESC` |- Web browser

| `order_details_form.php` | `INSERT INTO Order_Details (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)` |

| `order_details_form.php` (edit) | `UPDATE Order_Details SET order_id=?, product_id=?, quantity=?, subtotal=? WHERE order_detail_id=?` |### Installation (3 steps)

| `order_details_delete.php` | `DELETE FROM Order_Details WHERE order_detail_id=?` |

1. **Copy project to XAMPP**

#### Delivery   ```bash

| File | SQL Operation |   # Copy greencart folder to:

|------|---------------|   C:\xampp\htdocs\greencart

| `delivery_list.php` | `SELECT d.*, o.status AS order_status FROM Delivery d LEFT JOIN Orders o ON d.order_id=o.order_id ORDER BY delivery_id DESC` |   ```

| `delivery_form.php` | `INSERT INTO Delivery (order_id, delivery_method, delivery_status, estimated_time) VALUES (?, ?, ?, ?)` |

| `delivery_form.php` (edit) | `UPDATE Delivery SET order_id=?, delivery_method=?, delivery_status=?, estimated_time=? WHERE delivery_id=?` |2. **Start XAMPP**

| `delivery_delete.php` | `DELETE FROM Delivery WHERE delivery_id=?` |   - Start Apache

   - Start MySQL

### SQL Demonstrations

| File | Description |3. **Initialize Database**

|------|-------------|   - Open: `http://localhost/greencart/init_db_new.php`

| `sql_ops.php` | 40+ SQL operations with live examples (see SQL_REFERENCE.md) |   - Database and sample data created automatically!



---4. **Access Application**

   - Dashboard: `http://localhost/greencart/index.php`

## 🔥 SQL Operations Demonstrated   - SQL Showcase: `http://localhost/greencart/sql_ops.php`



### Complete List of SQL Demos in `sql_ops.php`### Default Login

```

1. **JOINs**: INNER, LEFT, RIGHT, CROSSUsername: admin

2. **Subqueries**: Scalar, Row, Table (IN), CorrelatedPassword: admin123

3. **EXISTS / NOT EXISTS**```

4. **Set Operations**: UNION, UNION ALL, INTERSECT, EXCEPT

5. **Aggregations**: COUNT, SUM, AVG, MIN, MAX## 📁 Project Structure

6. **GROUP BY & HAVING**

7. **Pattern Matching**: LIKE, REGEXP```

8. **CASE WHEN** (Conditional Logic)greencart/

9. **Window Functions**: ROW_NUMBER, RANK, DENSE_RANK├── index.php               # Dashboard with analytics

10. **Views** (Simulated)├── nav.php                 # Navigation component

11. **Stored Procedures** (Simulated)├── db.php                  # Database connection (PDO)

├── init_db_new.php         # Database setup with all SQL features

📖 **See `SQL_REFERENCE.md` for complete SQL code for all operations**├── sql_ops.php             # SQL operations showcase (40+ demos)

├── 

---├── customers_*.php         # Customer CRUD

├── vendors_*.php           # Vendor CRUD

## 🎯 How to Use├── products_*.php          # Product CRUD

├── orders_*.php            # Order CRUD

### 1. **Dashboard** (`index.php`)├── order_details_*.php     # Order Details CRUD

- View overall statistics├── delivery_*.php          # Delivery CRUD

- See recent orders with customer details└── README.md               # This file

- Top products by revenue```

- Customer spending leaderboard

## 🎨 What Makes This Special

### 2. **SQL Operations Demo** (`sql_ops.php`)

- Browse 40+ SQL examples### 1. Visual SQL Learning

- Click "View SQL" to see query codeEvery operation shows its SQL query:

- Click "Copy" to copy SQL to clipboard- **Hover tooltips** on dashboard cards

- Each demo shows live results from your database- **SQL code boxes** in operation showcase

- **Copy buttons** for easy learning

### 3. **Entity Management**- **Result tables** showing query output

Navigate using the top menu:

- **Customers**: Manage customer records### 2. Comprehensive SQL Coverage

- **Vendors**: Manage supplier informationFind implementations of:

- **Products**: Product catalog with vendor relationships- **Joins** - sql_ops.php lines 20-80

- **Orders**: Order management with customer linking- **Subqueries** - sql_ops.php lines 85-145

- **Order Details**: Line items for each order- **Aggregations** - sql_ops.php lines 180-220

- **Delivery**: Delivery tracking for orders- **Triggers** - init_db_new.php lines 200-250

- **Views** - init_db_new.php lines 150-200

### 4. **Hover Tooltips** ✨

On any list page:### 3. Professional Design

- **Hover over "Add" button** → See INSERT query- Modern Tailwind CSS styling

- **Hover over "Edit" link** → See UPDATE query with specific ID- Responsive layout

- **Hover over "Delete" link** → See DELETE query with specific ID- Font Awesome icons

- Gradient backgrounds

### 5. **Toggle SQL Display**- Hover effects

- Click **"View SQL"** button to show the query used on that page

- Click again to hide### 4. Working Application

- SQL code is syntax-highlighted and formattedNot just demos - it's a real system:

- Add/edit/delete customers, products, orders

---- Track inventory

- Monitor deliveries

## 🗄️ Database Schema- Generate reports



### Tables## 💡 Learning Guide



#### Customers### Find SQL Features

```sql

CREATE TABLE Customers (**Method 1: By Page**

  customer_id INT AUTO_INCREMENT PRIMARY KEY,- `init_db_new.php` - DDL, constraints, triggers, procedures, views

  name VARCHAR(100) NOT NULL,- `sql_ops.php` - Joins, subqueries, set operations, aggregations

  email VARCHAR(150) UNIQUE,- `index.php` - Dashboard queries with joins and aggregations

  phone VARCHAR(30),- `*_list.php` - SELECT with WHERE, ORDER BY, LIMIT

  address TEXT,- `*_form.php` - INSERT, UPDATE with validation

  preferences TEXT- `*_delete.php` - DELETE with foreign key handling

)

```**Method 2: Search Keywords**

Use Ctrl+F in your editor to search for:

#### Vendors- `JOIN`, `LEFT JOIN`, `INNER JOIN`

```sql- `GROUP BY`, `HAVING`

CREATE TABLE Vendors (- `UNION`, `EXCEPT`, `INTERSECT`

  vendor_id INT AUTO_INCREMENT PRIMARY KEY,- `TRIGGER`, `PROCEDURE`, `VIEW`

  vendor_name VARCHAR(150) NOT NULL,- `CASE WHEN`, `EXISTS`, `COALESCE`

  contact_email VARCHAR(150),

  phone VARCHAR(30),**Method 3: Interactive**

  location VARCHAR(200)1. Visit `http://localhost/greencart/sql_ops.php`

)2. Scroll through 20+ SQL demonstrations

```3. Click "View SQL" to see queries

4. Click "Copy" to use in your own code

#### Products

```sql## 📊 SQL Operation Index

CREATE TABLE Products (

  product_id INT AUTO_INCREMENT PRIMARY KEY,| Category | Operations | File | Lines |

  vendor_id INT,|----------|-----------|------|-------|

  name VARCHAR(150) NOT NULL,| **Joins** | INNER, LEFT, RIGHT, CROSS | sql_ops.php | 20-80 |

  category VARCHAR(100),| **Subqueries** | Scalar, Row, Table, Correlated | sql_ops.php | 85-145 |

  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,| **Set Ops** | UNION, UNION ALL, INTERSECT, EXCEPT | sql_ops.php | 150-180 |

  sustainability_tag VARCHAR(100),| **Aggregation** | GROUP BY, HAVING, COUNT, SUM, AVG | sql_ops.php | 185-225 |

  FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id) ON DELETE SET NULL| **Pattern** | LIKE, REGEXP | sql_ops.php | 230-250 |

)| **Views** | CREATE VIEW, SELECT FROM VIEW | init_db_new.php | 150-200 |

```| **Triggers** | BEFORE/AFTER INSERT/UPDATE | init_db_new.php | 200-250 |

| **Procedures** | CREATE PROCEDURE, CALL | init_db_new.php | 250-290 |

#### Orders

```sql## 🛠️ Technology Stack

CREATE TABLE Orders (

  order_id INT AUTO_INCREMENT PRIMARY KEY,- **Backend**: PHP 7.4+ with PDO

  customer_id INT,- **Database**: MySQL 5.7+ (8.0+ recommended)

  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,- **Frontend**: HTML5 + Tailwind CSS 3.0

  total_amount DECIMAL(10,2) DEFAULT 0.00,- **Icons**: Font Awesome 6.4

  status VARCHAR(50) DEFAULT 'pending',- **Server**: Apache (XAMPP)

  FOREIGN KEY (customer_id) REFERENCES Customers(customer_id) ON DELETE SET NULL

)## 📚 Course Alignment

```

Perfect for Database Systems courses covering:

#### Order_Details

```sql✅ **Relational Database Design** - Normalization, ER diagrams  

CREATE TABLE Order_Details (✅ **SQL Fundamentals** - SELECT, INSERT, UPDATE, DELETE  

  order_detail_id INT AUTO_INCREMENT PRIMARY KEY,✅ **Advanced SQL** - Joins, subqueries, set operations  

  order_id INT,✅ **Database Programming** - Triggers, procedures, views  

  product_id INT,✅ **Constraints** - Primary/foreign keys, CHECK, UNIQUE  

  quantity INT DEFAULT 1,✅ **Transactions** - ACID properties  

  subtotal DECIMAL(10,2) DEFAULT 0.00,✅ **Optimization** - Indexes, query performance  

  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,✅ **Web Integration** - PHP + MySQL applications  

  FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE SET NULL

)## 🎓 Educational Value

```

Students will learn:

#### Delivery

```sql1. **How to structure complex databases** with proper relationships

CREATE TABLE Delivery (2. **When to use different JOIN types** with practical examples

  delivery_id INT AUTO_INCREMENT PRIMARY KEY,3. **How subqueries simplify complex queries** 

  order_id INT,4. **Why triggers automate business logic**

  delivery_method VARCHAR(100),5. **How views provide data abstraction**

  delivery_status VARCHAR(100) DEFAULT 'scheduled',6. **Best practices for web database applications**

  estimated_time DATETIME,

  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE## 🔍 Sample SQL Queries

)

```### Dashboard Revenue (with JOIN)

```sql

#### View: CustomerOrderSummarySELECT COALESCE(SUM(o.final_amount), 0) as total

```sqlFROM orders o

CREATE VIEW CustomerOrderSummary ASWHERE o.status != 'cancelled'

SELECT c.customer_id, c.name, c.email, ```

COUNT(o.order_id) AS orders_count, 

COALESCE(SUM(o.total_amount),0) AS total_spent### Top Products (LEFT JOIN + GROUP BY)

FROM Customers c ```sql

LEFT JOIN Orders o ON c.customer_id = o.customer_idSELECT p.product_id, p.name, 

GROUP BY c.customer_id, c.name, c.email       COALESCE(SUM(od.quantity), 0) as total_sold

```FROM products p

LEFT JOIN order_details od ON p.product_id = od.product_id

### Sample DataGROUP BY p.product_id

Each table contains **6 sample rows** for demonstration purposes:ORDER BY total_sold DESC

- 6 Customers (Alice, Bob, Carol, David, Eve, Frank)LIMIT 5

- 6 Vendors (Fresh Farms, Eco Produce, Green Valley, Urban Harvest, Local Organics, Sunshine Goods)```

- 6 Products (Organic Apple, Banana, Almond Milk, Kale, Brown Rice, Quinoa)

- 6 Orders (varying amounts, some > $20 for demo queries)### Subquery Example

- 9 Order Details (line items for the orders)```sql

- 6 Delivery records (various statuses)SELECT * FROM products

WHERE price > (SELECT AVG(price) FROM products)

---ORDER BY price DESC

```

## 🎓 Educational Benefits

### View Usage

### For Students```sql

- Learn SQL by seeing it in actionSELECT * FROM sales_summary_view

- Understand CRUD operationsORDER BY total_revenue DESC

- Practice with real database structure```

- Copy queries for learning/reference

## 🐛 Troubleshooting

### For Teachers

- Ready-made teaching tool**Can't connect to database?**

- All SQL concepts in one place- Check `db.php` settings (default: localhost, root, no password)

- Professional UI students can explore- Ensure MySQL is running in XAMPP

- Easy to modify and extend

**Tables not created?**

### For Developers- Visit `init_db_new.php` first to initialize

- Reference for PHP + PDO patterns- Check for SQL errors displayed on page

- Clean code structure

- Modern UI implementation**Window functions not working?**

- Security best practices (prepared statements)- Requires MySQL 8.0+

- Still works fine with MySQL 5.7 (just skip window function demos)

---

## 📝 For Instructors

## 🔒 Security Features

This project demonstrates:

- ✅ **PDO Prepared Statements**: All queries use parameterized queries- [x] Complete SQL coverage (40+ operations)

- ✅ **Input Sanitization**: `htmlspecialchars()` on all output- [x] Interactive visual learning

- ✅ **SQL Injection Prevention**: No string concatenation in queries- [x] Professional code quality

- ✅ **XSS Protection**: All user input escaped before display- [x] Comprehensive documentation

- [x] Easy setup and grading

---- [x] Real-world application



## 📚 Documentation FilesEvery SQL concept is:

- **Indexed** - Easy to find in code

- **README.md** (this file) - Overview and usage guide- **Documented** - Comments explain each operation

- **SQL_REFERENCE.md** - Complete SQL code reference for all queries- **Visualized** - Queries shown with results

- **Working** - Not just demos, fully functional

---

## 🎯 Future Enhancements

## 🚀 Future Enhancements

- User authentication system

Possible extensions:- Export reports to PDF

- User authentication system- Advanced analytics charts

- Advanced search/filtering- Email notifications

- Export to CSV/PDF- API endpoints (REST)

- Email notifications- Mobile responsive improvements

- REST API endpoints

- More complex reports## 📞 Support

- Data visualization charts

Questions about SQL features?

---1. Check hover tooltips in the interface

2. Review `sql_ops.php` for examples

## 📝 License3. Read inline code comments

4. Search this README

This is an educational project. Feel free to use, modify, and learn from it.

## 📄 License

---

Educational project for Database Systems coursework.

## 👨‍💻 Developer Notes

---

### File Naming Convention

- `{entity}_list.php` - Display all records**🌿 GreenCart** - Professional Grocery Management System  

- `{entity}_form.php` - Add/Edit form*Making Database Systems Visual and Interactive*

- `{entity}_delete.php` - Delete confirmation

**Built for:** Database Systems Course  

### Code Standards**Purpose:** Demonstrate all major SQL operations  

- PHP 8.2+ features used**Approach:** Visual + Interactive + Professional  

- PSR-12 coding style

- Responsive design (Tailwind)### Quick Links

- Clean separation of concerns- 🏠 [Dashboard](http://localhost/greencart/index.php)

- 💻 [SQL Operations](http://localhost/greencart/sql_ops.php)

---- 🗄️ [Database Setup](http://localhost/greencart/init_db_new.php)



## 🆘 Troubleshooting---



### Database Connection Issues*Hover over any data to see the SQL that powers it! 🎯*

- Check `db.php` for correct credentials
- Ensure MySQL is running in XAMPP
- Database name: `greencart_db`

### SQL Demos Not Working
- Run `init_db.php?reset=1` to reset database
- Ensure MySQL version supports window functions (8.0+)
- Check PHP error logs in XAMPP

### Tooltips Not Showing
- Clear browser cache
- Check browser console for JavaScript errors
- Ensure CSS is loading properly

---

## 📧 Contact

For questions or suggestions about this educational project, feel free to modify and improve!

---

**Built with ❤️ for SQL education**
