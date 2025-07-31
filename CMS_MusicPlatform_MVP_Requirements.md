# Requirements

## 1. Overview
Build a combined CMS + Music Platform MVP using plain PHP, Bulma CSS, jQuery AJAX, MariaDB/MySQL, following OOP best practices.

## 2. Functional Requirements

### 2.1 CMS Features
- CRUD for blog posts
- Import existing posts (e.g., Nathan’s)
- Employee management module
- Admin panel example for online store
- REST API endpoints for content
- Sync with WordPress or other CMS
- Core CMS (“Kantent”) foundation

### 2.2 Music Platform MVP
- Audio player (Howler.js) with play/pause, volume, progress
- Playlist creation & management
- Restrict playback to 10 seconds for unregistered users
  - Show modal prompting registration
- Registered users can “add to cart”
- View copyright info, link to external
- Grid of genres & songs (hot from blog data)
- Responsive search bar
- “Create listing” form: upload video + tags + file renaming
- On listing match, trigger Slack webhook & send email

## 3. Non-Functional Requirements
- PHP 8+, MariaDB/MySQL
- Bulma for styling; jQuery AJAX for dynamic calls
- PHPUnit tests: ≥90% coverage
- PSR-4 autoloading, SOLID OOP
- Secure input validation & escaping
- Config via `.env`

## 4. Acceptance Criteria
- All unit tests pass
- Player controls fully tested
- Auth enforcement works (modal appears)
- Data persistence verified
- Slack/email integration tested
- Responsive UI on desktop/mobile
- Documentation complete for go-live

## 5. Milestones
1. Boilerplate & DB setup  
2. CMS CRUD + import  
3. Music player & UI  
4. Registration & auth  
5. Listing form + integrations  
6. Testing & documentation  
7. Launch prep
