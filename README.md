# Audiobooke

A comprehensive web portal for audiobook enthusiasts, powered by Librivox data. This project provides a modern, full-stack application for browsing, searching, and downloading free audiobooks with user authentication and favorites functionality.

## 🎯 Project Overview

Audiobooke is a microservices-based web application that serves as a portal for audiobook enthusiasts. It features a modern Angular frontend with server-side rendering, a Yii2 PHP backend API, and is deployed on AWS infrastructure using CDK.

### Key Features

**Current Features:**
- Browse and download 10,000+ free audiobooks from Librivox
- Advanced search and filtering by title, author, language
- User authentication with Google SSO
- Add audiobooks to favorites
- Responsive web interface with Material Design
- Server-side rendering for optimal SEO

**Planned Features:**
- User reviews and ratings system
- Personal collections and playlists
- Advanced search filters and recommendations

## 🏗️ Architecture

### Technology Stack

**Frontend:**
- **Angular 9** with TypeScript
- **Angular Material** for UI components
- **Apollo GraphQL** for data fetching
- **Server-Side Rendering (SSR)** with Angular Universal
- **Flex Layout** for responsive design

**Backend:**
- **PHP 7.1+** with **Yii2 Framework**
- **REST API** architecture
- **MySQL** database (MariaDB 10.2)
- **Redis** for caching
- **OAuth2** for authentication

**Infrastructure:**
- **Docker & Docker Compose** for containerization
- **AWS CDK** for infrastructure as code
- **ECS (Elastic Container Service)** for container orchestration
- **AWS Batch** for background processing
- **RDS** for managed database
- **Application Load Balancer** for traffic distribution

### System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Angular)     │◄──►│   (Yii2 API)    │◄──►│   (MySQL)       │
│   Port: 8888    │    │   Port: 80      │    │   Port: 33306   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   NGINX         │    │   Redis         │    │   AWS CDK       │
│   (Reverse      │    │   (Caching)     │    │   (Infrastructure)│
│    Proxy)       │    │   Port: 6379    │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 📁 Project Structure

```
audiobooke/
├── apps/
│   ├── frontend/                 # Angular application
│   │   ├── src/
│   │   │   ├── app/
│   │   │   │   ├── components/   # Reusable UI components
│   │   │   │   ├── modules/      # Feature modules
│   │   │   │   ├── services/     # Data and utility services
│   │   │   │   └── models/       # TypeScript interfaces
│   │   │   └── assets/           # Static assets
│   │   └── package.json
│   ├── backend/                  # Yii2 PHP API
│   │   ├── backend/              # Web application
│   │   ├── rest/                 # REST API endpoints
│   │   │   └── versions/v1/
│   │   │       └── controllers/  # API controllers
│   │   ├── common/               # Shared models and config
│   │   │   └── models/           # Database models
│   │   └── composer.json
│   ├── nginx/                    # Reverse proxy configuration
│   ├── mysql/                    # Database initialization
│   └── api-docs/                 # API documentation
├── cdk/                          # AWS CDK infrastructure
│   ├── stacks/                   # CDK stack definitions
│   │   └── constructs/           # Reusable CDK constructs
│   └── package.json
├── docker-compose.yml            # Local development setup
└── package.json                  # Root project configuration
```

## 🚀 Getting Started

### Prerequisites

- **Node.js** (v12 or higher) and **npm**
- **Docker** and **Docker Compose**
- **Git**

### Local Development Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Dzhuneyt/Audiobooke.git
   cd Audiobooke
   ```

2. **Install dependencies:**
   ```bash
   npm install
   npm run util:install-dependencies
   ```

3. **Start the development environment:**
   ```bash
   npm run dev
   ```

4. **Access the application:**
   - **Frontend:** http://localhost:8888
   - **API Documentation:** http://localhost:8889
   - **Database:** mysql://localhost:33306 (user: audiobooke, pass: audiobooke)

### Environment Variables

Create a `.env` file in the root directory with the following variables:

```env
# Database Configuration
DB_USER=audiobooke
DB_PASS=audiobooke
DB_NAME=audiobooke
MYSQL_HIDDEN_PORT=33306

# Google OAuth (for SSO)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_SSO_REDIRECT_URL=http://localhost:8888/user/login

# SMTP Configuration (optional)
SMTP_HOST=smtp.gmail.com
SMTP_USER=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_PORT=587

# Application Configuration
EXPOSED_PORT=8888
YII_ENV=dev
```

## 🏗️ API Documentation

### REST API Endpoints

The backend provides a RESTful API with the following main endpoints:

#### Audiobooks
- `GET /api/v1/audiobooks` - List all audiobooks with pagination
- `GET /api/v1/audiobooks/{id}` - Get specific audiobook details
- `GET /api/v1/audiobooks/topten` - Get top 10 audiobooks
- `POST /api/v1/audiobooks/{id}/favorite` - Add audiobook to favorites
- `GET /api/v1/audiobooks/{id}/download` - Download audiobook files

#### Authors
- `GET /api/v1/authors` - List all authors
- `GET /api/v1/authors/{id}` - Get specific author details

#### Users
- `GET /api/v1/users/profile` - Get user profile
- `POST /api/v1/users/login` - User authentication
- `POST /api/v1/users/logout` - User logout

#### Health Check
- `GET /api/v1/healthcheck` - API health status

### Database Schema

#### Core Tables

**audiobook**
- `id` - Primary key
- `title` - Audiobook title
- `description` - Audiobook description
- `language` - Language code
- `copyright_year` - Copyright year
- `num_sections` - Number of audio sections
- `url_zip_file` - Download URL
- `totaltimesecs` - Total duration in seconds
- `type` - Source type (librivox/audible)

**author**
- `id` - Primary key
- `name` - Author name
- `birth_year` - Birth year
- `death_year` - Death year

**user**
- `id` - Primary key
- `email` - User email
- `username` - Username
- `auth_key` - Authentication key
- `password_hash` - Hashed password
- `status` - User status

**audiobook_favorite**
- `id` - Primary key
- `id_user` - User ID
- `id_audiobook` - Audiobook ID
- `created_at` - Creation timestamp

## 🚀 Deployment

### AWS Infrastructure Deployment

The project uses AWS CDK for infrastructure management. The CDK app is located in the `/cdk` directory.

#### Prerequisites

- **AWS CLI** configured with appropriate credentials
- **Node.js** and **npm**
- **AWS CDK CLI** installed globally: `npm install -g aws-cdk`

#### Deployment Steps

1. **Navigate to CDK directory:**
   ```bash
   cd cdk
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment:**
   Create a `.env` file in the `cdk` directory:
   ```env
   ENV_NAME=production  # or staging, development
   AWS_REGION=us-east-1
   ```

4. **Deploy infrastructure:**
   ```bash
   npm run deploy:app
   ```

#### Deployed Resources

The CDK deployment creates:

- **VPC** with public and private subnets
- **ECS Cluster** for container orchestration
- **Application Load Balancer** for traffic distribution
- **RDS Database** (MySQL) for data storage
- **AWS Batch** for background processing
- **Secrets Manager** for secure credential storage
- **CloudWatch** for monitoring and logging

### Production Considerations

1. **Database Setup:**
   - The backend expects database credentials in AWS Secrets Manager
   - Configure RDS instance and update secrets accordingly

2. **SSL/TLS:**
   - Configure SSL certificates for production domains
   - Update NGINX configuration for HTTPS

3. **Monitoring:**
   - Set up CloudWatch alarms for application health
   - Configure log aggregation and analysis

4. **Security:**
   - Review and update security groups
   - Implement proper IAM roles and policies
   - Enable AWS WAF for additional protection

## 🛠️ Development

### Available Scripts

**Root level:**
- `npm run dev` - Start development environment
- `npm run util:install-dependencies` - Install all dependencies
- `npm run backend:migrations` - Run database migrations

**Frontend (apps/frontend):**
- `npm start` - Start development server
- `npm run build` - Build for production
- `npm run test` - Run unit tests
- `npm run lint` - Run linting

**Backend (apps/backend):**
- `composer install` - Install PHP dependencies
- `php yii migrate` - Run database migrations
- `php yii serve` - Start development server

### Code Structure

#### Frontend Architecture

The Angular application follows a modular architecture:

- **Components:** Reusable UI components (header, sidebar, audiobook cards)
- **Services:** Data fetching, authentication, analytics
- **Modules:** Feature-based modules (audiobook, home, user)
- **Models:** TypeScript interfaces for data structures

#### Backend Architecture

The Yii2 application follows MVC pattern:

- **Controllers:** Handle HTTP requests and responses
- **Models:** Database entities and business logic
- **Actions:** Specific API endpoint implementations
- **Services:** Business logic and external integrations

### Testing

- **Frontend:** Angular unit tests with Jasmine/Karma
- **Backend:** PHP unit tests with Codeception
- **E2E:** Protractor for end-to-end testing

## 🔧 Troubleshooting

### Common Issues

1. **Docker containers not starting:**
   ```bash
   docker-compose down
   docker-compose up --build
   ```

2. **Database connection issues:**
   - Verify MySQL container is running
   - Check database credentials in environment variables
   - Ensure database is initialized

3. **Frontend build errors:**
   ```bash
   cd apps/frontend
   npm install
   npm run build
   ```

4. **Backend API errors:**
   - Check Yii2 logs in `apps/backend/backend/runtime/logs/`
   - Verify database migrations are up to date
   - Check environment configuration

### Logs

- **Frontend:** Check browser console and Angular logs
- **Backend:** Check `apps/backend/backend/runtime/logs/`
- **Docker:** `docker-compose logs [service-name]`
- **AWS:** CloudWatch logs for production

## 📚 Additional Resources

- [Angular Documentation](https://angular.io/docs)
- [Yii2 Framework Guide](https://www.yiiframework.com/doc/guide/2.0/en)
- [AWS CDK Documentation](https://docs.aws.amazon.com/cdk/)
- [Docker Documentation](https://docs.docker.com/)

## 🤝 Contributing

This project is currently archived and no longer actively maintained. However, the codebase serves as a comprehensive example of:

- Modern full-stack web application architecture
- Microservices implementation with Docker
- AWS infrastructure as code with CDK
- Angular + Yii2 integration
- Production-ready deployment patterns

## 📄 License

This project is licensed under the ISC License.

## 👨‍💻 Author

**Dzhuneyt** - Software Development
- GitHub: [@Dzhuneyt](https://github.com/Dzhuneyt)
- Website: [dzhuneyt.com](https://dzhuneyt.com)

---

*This project demonstrates modern web development practices and serves as a reference implementation for full-stack applications with microservices architecture.*
