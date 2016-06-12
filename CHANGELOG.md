# Change Log
All notable changes to this project will be documented in this file.

## [3.3.1] - 2016-06-12
### Added:
 - isLoggedIn method
 - Response and Request refactoring
 - Better exception messages in ProviderLoginCheckWrapper

## [3.2.3] - 2016-06-09
### Fixes:
 - Pins like/dislike
 - Response error check

## [3.2.2] - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns terms and their positions.

## [3.2.1] - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns items concatenated with the query request.

## [3.2.0] - 2016-06-04
### Added:
 - Keywords provider
 	- recommendedFor($query) method.

## [3.1.0] - 2016-06-04
### Added:
 - Change board (title, description, category and privacy)

## [3.0.0] - 2016-05-29
### Changed:
 - News *latest* method renamed to *last*
 - Request and providers refactoring
 
### Fixes:
 - Boards follow/unfollow request

## [2.6.0] - 2016-05-28
### Updated:
 - Pagination refactoring
 - Requests refactoring
 - version up due to the previous features

## [2.5.3] - 2016-05-28
### Added: 
- Edit profile image
- Get pins from specific source

## [2.5.2] - 2016-05-23
### Added: 
- Edit pin by id (change description, link and board)
- Move pin to a new board

## [2.5.1] - 2016-04-22
### Fixed:
- Cookie file now is placed in os PHP temporary directory

## [2.5.0] - 2016-03-12
### Added:
- Specify link when creating pin as third argument

## [2.4.2] - 2016-03-03
### Fixed:
- Login requirements check for some methods

## [2.4.1] - 2016-03-01
### Fixed:
- sendMessage, sendMail array bugs fix

## [2.4.0] - 2016-03-01
### Added:
1. Changes in sendMessage:
	- Many users at once
	- Add pin to message
2. Added sendMail method. Works like sendMessage, but pass email or array of emails instead of users ids.

## 2016-02-11
- Throws exception when login fails
- Tests refactoring

## 2016-02-06
- Providers refactoring (loginRequired check before every method call added)
- New providers: User for edit user settings (profile for example) and News for fetching user related news.
