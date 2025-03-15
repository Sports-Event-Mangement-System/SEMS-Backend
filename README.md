# Sports Event Management System (Backend)

A RESTful API backend for managing sports events, teams, players, and matches. Designed to work with a frontend application.

## 🚀 Installation Steps

1. **Clone the repository**
```bash
git clone https://github.com/Sports-Event-Mangement-System/SEMS-Backend.git
cd SEMS-Backend
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**  
   Create a MySQL database and update `.env` file:
```env
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed Database (Optional)**  
   Populate with sample data for testing:
```bash
php artisan db:seed
```
   *Creates dummy users, tournaments, teams, matches, and other test data*

7. **Start server**
```bash
php artisan serve
```


## 📋 API Endpoints List

### 🔐 Authentication
- `POST /api/login` - User login  
- `POST /api/register` - User registration  
- `POST /api/logout` 🔒 - User logout  
- `GET /api/user/{id}` 🔒 - Get user details  
- `POST /api/update/user/{id}` 🔒 - Update user information  
- `POST /api/update/profile_image/{id}` 🔒 - Update profile image  
- `DELETE /api/delete/profile_image/{id}` 🔒 - Delete profile image  

### 📊 Dashboard
- `GET /api/dashboard` 🔒 - Get dashboard stats

### 📧 Contacts
- `GET /api/contacts` 🔒 - List all contacts
- `GET /api/show/contacts/{id}` - Get contact details
- `POST /api/store/contacts` - Create new contact
- `DELETE /api/delete/contacts/{id}` 🔒 - Delete contact

### 🏆 Tournaments
- `GET /api/show/tournament/{id}` - Get tournament details
- `GET /api/active/tournaments` - List active tournaments
- `GET /api/tournaments` 🔒 - List all tournaments
- `POST /api/store/tournaments` 🔒 - Create new tournament
- `GET /api/edit/tournament/{id}` 🔒 - Edit tournament data
- `POST /api/update/tournament/{id}` 🔒 - Update tournament
- `DELETE /api/delete/tournament/{id}` 🔒 - Delete tournament
- `POST /api/update-status/tournament/{id}` 🔒 - Update tournament status

### 🏅 Teams
- `GET /api/teams/tournament/{id}` - List teams by tournament
- `GET /api/show/team/{id}` - Get team details
- `GET /api/teams` 🔒 - List all teams
- `POST /api/store/team` 🔒 - Create new team
- `POST /api/update/team/{id}` 🔒 - Update team details
- `POST /api/update-status/team/{id}` 🔒 - Update team status
- `DELETE /api/delete/team/{id}` 🔒 - Delete team
- `POST /api/follow/team/{id}` 🔒 - Follow a team

### 👤 Players
- `GET /api/players` 🔒 - List all players

### 🗓 Schedules
- `GET /api/tiesheet/tournament/{id}` 🔒 - Generate tiesheet

### ⚽ Matches
- `GET /api/tournament/matches` - List all matches
- `GET /api/match/details/{id}` - Get match details
- `GET /api/predict/match/{id}` - Predict next match
- `POST /api/save/matches/tournament/{id}` 🔒 - Save tournament matches
- `GET /api/tiesheet/response/{id}` 🔒 - Get tiesheet response
- `DELETE /api/delete/tiesheet/{id}` 🔒 - Delete tiesheet
- `POST /api/update/match/{id}` 🔒 - Update match details

### ⚙️ Site Settings
- `GET /api/site/settings` - Get site settings
- `POST /api/update/site/email/settings` 🔒 - Update email settings

## 🛠️ Requirements

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js 16+ (for frontend integration)

## 🔧 Configuration

- Use Postman or similar tool to test APIs
- All API requests need `Accept: application/json` header
- Protected routes require authentication token
- Use `php artisan db:seed` to populate development data
- Seeders include:
  - 2 sample tournaments
  - 13 teams with 42 players
  - 28 scheduled matches
  - Test user Admin/User accounts

## 🔑 Test Accounts Created by Seeder:
```bash
Admin Account: 👨🏻‍💻
Email: admin@admin.com
Password: password

Regular User: 👨🏻‍💼
Email: user@mail.com
Password: password
```

## 🤝 Contributing
Feel free to submit pull requests or open issues for any improvements.
