# Project Finance Management Application

## General Overview
The Project Finance Management application is a comprehensive financial management system developed using Laravel and Filament. Below is a general overview of the project, broken down by module:

### Company Management
- **Companies**: Management of registered companies in the system
  - Company details (name, email, phone)
  - Contact information and address management
  - Company-based permission and authorization system

### Project Management
- **Projects**: Management of projects linked to companies
  - Project timeline (start and end dates)
  - Assignment of project managers
  - Project status tracking (active/inactive)
- **Tasks**: Management of tasks linked to projects
  - Task assignment and tracking system
  - Task status and priority management

### Accounting Management
- **Accounts**: Management of financial accounts
  - Account balance tracking
  - Categorization with account groups
  - Integration with the Uniform Chart of Accounts
- **Transactions**: Recording and tracking of financial transactions
  - Transaction groups and categorization
  - Income-expense tracking

### User Management
- **Users**: Management of system users
- **Roles & Permissions**: Authorization system
  - Role-based access control
  - Customized permission management

### Dashboard and Reporting
- **Widgets**: Summary views for recent companies, projects, and transactions
- Financial status summary
- Project and task status tracking

## Technology and Features
The application provides a modern and user-friendly interface using the Filament admin panel. It is designed to support multiple languages, making it accessible in various languages. Additionally, it features a containerized structure with Docker, enabling easy setup and deployment.

## Data Relationships
Each module is interconnected through a relational database structure. For example, projects are linked to companies, tasks to projects, and accounts to users. This structure creates a comprehensive ecosystem for finance and project management.