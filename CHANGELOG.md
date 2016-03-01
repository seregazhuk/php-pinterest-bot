## 2016-03-01
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