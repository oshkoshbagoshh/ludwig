# Style Guidelines

## PHP Coding Standards

1. **PSR-4 Autoloading**: Follow PSR-4 autoloading standard for class namespaces and file organization.
2. **SOLID Principles**: Adhere to SOLID object-oriented design principles.
3. **Naming Conventions**:
   - Classes: PascalCase (e.g., `UserController`)
   - Methods/Functions: camelCase (e.g., `getUserById()`)
   - Variables: camelCase (e.g., `$userCount`)
   - Constants: UPPER_SNAKE_CASE (e.g., `MAX_LOGIN_ATTEMPTS`)
4. **Indentation**: Use 4 spaces for indentation, not tabs.
5. **Line Length**: Keep lines under 120 characters when possible.
6. **Comments**: Use DocBlocks for classes and methods with `@param`, `@return`, and `@throws` annotations.

## Database Conventions

1. **Table Names**: Use snake_case and plural (e.g., `users`, `track_genres`).
2. **Column Names**: Use snake_case (e.g., `first_name`, `created_at`).
3. **Primary Keys**: Use `id` as the primary key name.
4. **Foreign Keys**: Use singular table name followed by `_id` (e.g., `user_id`).
5. **Timestamps**: Include `created_at` and `updated_at` columns in all tables.

## Frontend Standards

1. **CSS Framework**: Use Bulma CSS for styling components.
2. **JavaScript**: Use jQuery for DOM manipulation and AJAX requests.
3. **Responsive Design**: Ensure all UI components work on mobile, tablet, and desktop.
4. **Accessibility**: Maintain WCAG 2.1 AA compliance for all user interfaces.

## Documentation

1. **Code Documentation**: Document all classes, methods, and complex logic.
2. **README Files**: Include clear README files for major components.
3. **Change Logs**: Document all significant changes in CHANGELOG.md.

## Testing

1. **Coverage**: Maintain at least 90% code coverage with unit tests.
2. **Test Naming**: Name tests descriptively (e.g., `testUserCanCreatePlaylist()`).
3. **Assertions**: Use specific assertions rather than generic ones.

## Version Control

1. **Commit Messages**: Write clear, descriptive commit messages in present tense.
2. **Branching**: Use feature branches for new development.
3. **Pull Requests**: Include descriptions and reference related issues.

## Security

1. **Input Validation**: Validate all user inputs.
2. **SQL Injection**: Use prepared statements for all database queries.
3. **XSS Prevention**: Escape output to prevent cross-site scripting.
4. **Authentication**: Implement proper authentication checks for all protected routes.