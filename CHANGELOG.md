# Change Log

## 1.0.5 First open source release

### Breaking Changes
- Admin users can now block users. This requires a table migration. Apply `/database/migrations/2022 05 14 Add Is Blocked To USERS.sql` for existing installs `v1.0.4` and earlier. See "How to update Qur'an Tools" in the readme.

### Other
- Users can update their own email addresses
- Admins can delete users
- Basic Contact Us feature added to Help menu
- Privacy policy link added to Help -> Legal menu (if one is provided)
- Various security enhancements
- Various refactorings and tidy-ups

## 1.0.4 Branding

- Allow organisations hosting Qur'an Tool to add their own basic text branding to the site

## v.1.0.3 Rejig database install

- Replace zip file of initial database install with individual SQL files

## v.1.0.2 Understanding new releases

- Added `changelog.md`
- Removed bad HTML comment in `about.php`
- Added Michael and Andy to the ABOUT page
- fix `FILTER_VALIDATE_BOOLEAN` for sign-ups
