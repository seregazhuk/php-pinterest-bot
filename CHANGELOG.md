# Change Log
All notable changes to this project will be documented in this file.

## v5.9.1 - 2019-02-13
### Fixed
 - Log in issue

## v5.9.0 - 2018-12-29
### Added:
 - Support for Reach Pins. You can specify a title when creating/editing a pin.
 - Allow to specify a board section when create/edit Pins.
### Fixed:
 - Uploads from non-ascii links

## v5.8.2 - 2018-08-17
### Added:
 - Pinners `isFollowedByMe()` helper method
 
### Fixed:
 - Password `reset()` method

## v5.8.1 - 2018-07-31
### Fixed
 - Added separate method for retrieving board invites (accepted and declined) 

## v5.8.0 - 2018-07-07
### Added:
 - Board `leave()`
### Updated
 - Required PHP version is set to 7.0  

## v5.7.3 - 2018-06-23
### Fixed:
 - Retrieving pinner's followers and following requires being logged in

## v5.7.2 - 2018-06-10
### Fixed:
 - board sections should require being logged in
 - providers resolution for multi-case name 

## v5.7.1 - 2018-05-13
### Fixed
 - endpoint for notification details

## v5.7.0 - 2018-05-06
### Added:
 - get details for a notification

## v5.6.9 - 2018-03-21
### Added:
 - get comments for a specified pin

### Fixed:
 - likes/dislikes are no longer supported by Pinterest and have been removed

## v5.6.8 - 2018-03-20
### Fixed:
 - clear errors after success response

## v5.6.7 - 2018-03-20
### Fixed:
 - writing comments for pins
 - improved testing post requests 

## v5.6.6 - 2018-02-23
### Added:
 - hash-tags typeahead suggestions
### Changed: 
 - suggestions `getForQuery()` renamed to `searchFor()`

## v5.6.5 - 2018-02-13
### Changed:
 - Board sections `edit()` renamed to `update()`

## v5.6.4 - 2018-02-09
### Added:
 - Inbox `getMessages()` to receive conversation messages

## v5.6.3 - 2018-02-04
### Fixed:
 - pagination for inbox conversations

## v5.6.2 - 2018-01-19
### Fixed:
 - crash for recommended keywords with no response 
 - board invites require to be logged in
 - hide link when creating a pin if not explicitly provided 

## v5.6.1 - 2017-12-31
### Fixed:
 - add login requirement for board sections

## v5.6.0 - 2017-12-25
### Added:
 - Boards `my()` method (alias for method `forMe()`)
 - BoardSections provider

## v5.5.9 - 2017-12-02
### Fixed:
 - use multibyte functions only if appropriate extension is installed

## v5.5.8 - 2017-11-29
### Fixed:
 - Search functionality for pinners requires a bot to be logged in. Now `pinners->search()` call throws `AuthRequired` exception if not logged in.

## v5.5.7 - 2017-11-22
### Fixed:
 - Helper functions auto-loading

## v5.5.6 - 2017-11-21
### Fixed:
 - Use registration form in business register
 - move helper functions to PinterestBot namespace 

## v5.5.5 - 2017-11-12
### Fixed:
 - Multi-byte str-functions call

## v5.5.4 - 2017-10-28
### Added:
 - Type-ahead suggestions for search query

## v5.5.3 - 2017-08-13
### Fixed:
 - Boards `forMe()` method requires a bot to be logged-in.
 - Boards `update()` method works with titles which has spaces.
 - Inbox `contactRequests()` method requires being logged in.
 - Inbox `ignoreContactRequests()` method renamed to `ignoreContactRequest.
 - Pins `moveToBoard()` removed pins description.
 - PHPUnit version updated to 5.7.
 - Pins `visualSimilar()` returns Pagination.
 - Password `reset()` for invalid links.
 
### Added:
 - Pins `share()` to get link for sharing a pin and asking for reacting (like/dislike).
 - Pins `reactAsGood()`/`reactAsBad()` to react when you were given a sharing link with a pin.
 - Improved tests coverage
  
### Removed:
 - `wait()` method

## v5.5.2 - 2017-07-22
### Fixed:
 - Profile update

## v5.5.1 - 2017-07-10
### Changed:
 - Moved to PHP version 5.6
 - Login required check refactoring
 
### Fixed: 
 - Profile form

## v5.5.0 - 2017-07-01
### Added:
 - User `id()` helper to get current user id
 - Boards `sendInvite`, `acceptInvite`, `ignoreInvite` and `removeInvite`
 - Contact requests

## v5.4.5 - 2017-06-27
### Fixed:
 - Boards invites

## v5.4.4 - 2017-06-23
### Added:
 - Pins TryIt functionality
 
### Fixed:
 - Registration
 - Convertion to business account requires being logged in
 - Updated Pinterest X-APP-VERSION header

## v5.4.3 - 2017-06-16
### Fixed:
 - Issue with autologin
 - Issue with creating a cookie file for every new session

## v5.4.2 - 2017-06-07
### Fixed:
 - Edit/repin pins

## v5.4.1 - 2017-05-31
### Fixed:
 - Added dependency for fileinfo extension

## v5.4.0 - 2017-05-28
### Fixed:
 - Possible bug in `useProxy` when providing empty auth string
 - Numbers in api requests should always be cast to strings

### Added:
 - User sessions history
 - Profile setting `excludeFromSearch($bool)` to exclude your account from search results
 - Profile setting `setLocal($locale)`
 - Profile setting `setAccountType($type)`
 - User `getCountries()` to list all avaiable countries for user profile
 - Boards `invites()`
 - Pins `analytics($pinId)`

## v5.3.11 - 2017-05-23
### Fixed:
 - Cookies loading

## v5.3.10 - 2017-05-23
### Fixed:
 - Cookies error on empty filename

## v5.3.9 - 2017-05-07
### Fixed:
 - Boards *forMe()* and Pinners *followers*() methods

### Added:
 - httpClient *usesProxy* method

## v5.3.8 - 2017-05-07
### Fixed:
 - boards *info()* method for boards with spaces in their names

## v5.3.7 - 2017-05-07
### Fixed:
 - resolving only providers from container
 - removed Bot unnecessary abstraction layer

## v5.3.6 - 2017-05-01
### Added
 - bot *wait()* method

## v5.3.5 - 2017-04-23
### Fixed:
 - Bot *getClientInfo()* method

## v5.3.4 - 2017-04-23
### Fixed:
 - Boards *forMe()* method
 - Pinners *followers()* uses limit

## v5.3.3 - 2017-04-22
### Updated:
 - Pinterest request headers update

## v5.3.2 - 2017-04-17
### Fixed:
 - Last error message. First goes message, then code if message is empty

## v5.3.1 - 2017-04-14
### Fixed:
 - Storing last error from response
 - Registration

## v5.3.0 - 2017-04-09
### Added
 - examples
 - Topics *explore* method for getting trending topics
 - Pins *explore* method for getting trending pins

### Fixed:
 - Annotations for *comments* in Bot class

## v5.2.7 - 2017-04-02
### Added:
 - Auth *register* and *registerBusiness* methods accept Regstration form object.
 - User *profile* method accepts Profile form object.
 - Pinners *followers* method returns current user's followers when used without arguments.
 - Boards *forMe* method to get boards of current logged-in user.

### Fixed:
 - Boards *forMe* and *forUser* methods always return array. For no results they
 return empty array.

## v5.2.6 - 2017-03-25
### Added:
 - HttpClient *dontUseProxy()* method to stop sending requests via proxy

### Fixed:
 - Additional info added in pins *related()* method.
 - Auth *login()* method always returns true, if proxy doesn't work

## v5.2.5 - 2017-03-18
### Fixed:
 - Pagination fails on last response.

## v5.2.4 - 2017-03-16
### Added:
 - Pinners *tried* pins.
 - Pinners follow and unfollow methods accept both user's id or username.

### Fixed:
 - Pins *activity()* and *tried()* methods always return Pagination object.
 - Boards *forUser* method returns detailed information for every board. If no boards
 are available it returns empty array.
  - Pagination processes *getResponseData()* as array

## v5.2.3 - 2017-01-24
### Fixed:
 - Registration

## v5.2.2 - 2017-01-24
### Fixed:
 - Search method correctly works with limits.

## v5.2.1 - 2017-01-22
### Fixed:
 - Pinners *followers()* methods.

## v5.2.0 - 2017-01-08
### Added:
 - User *clearSearchHistory()* to remove search suggestions.
 - Pins *tied()* returns the pinners who have tied this pin.
 - Pins *searchInMyPins()* method.
 - Inbox provider.
### Removed:
 - News and Conversations providers, their methods moved to Inbox provider.

## v5.1.7 - 2016-12-31
### Fixed:
 - Receiving confirmation email after registering business account.

## v5.1.6 - 2016-12-25
### Fixed:
 - Feed pagination error

## v5.1.4 - 2016-12-20
### Fixed:
 - Pagination issue with only first result.
 - Request headers.
 
### Added:
 - *getRawData()* method to Response to get the original response data from Pinterest.
 - More detailed registration process.

## v5.1.3 - 2016-12-12
### Fixed:
 - Pagination offset.
 
### Added:
 - *take* method to pagination to set limits.

## v5.1.2 - 2016-12-11
### Fixed:
 - Registration now sends confirmation emails. 
 
### Added:
 - *Skip* method to pagination.

## v5.1.1 - 2016-12-03
### Added:
 - To any pagination added *toArray* method to receive all pagination results as array. 

### Fixed:
 - Pins *activity* now returns an empty error for no results instead of null. 

## v5.1.0 - 2016-12-02
### Added:
 - Boards *createPrivate*, *sendWithMessage* and *sendWithEmail* methods.
 - Pins *sendWithMessage*, *sendWithEmail* and *saveOriginalImage* methods.
 - HttpClient *useProxy* and *useSocksProxy* methods.
 
### Changed:
 - Removed $removeCookies param from *Auth::logout()* method. Use 
 *bot->getHttpClient()->removeCookies()* to remove your cookies.

## v5.0.1 - 2016-11-13
### Fixed:
 - Interests *main* method requires to be logged in.

## v5.0.0 - 2016-11-08
### Added:
 - $removeCookies param to *logout* method.
 - *block* method in Pinners provider.
 - *getCurrentUrl* method to receive the last visited page url.
 - Pins *copy*, *move*, *deleteFromBoard* and *send* methods.
 - Pinners *blockById* method.
 - Boards *send* method.
 
### Changed:
 - *login*, *logout*, *register*, *registerBusiness*, *isLoggedIn* moved to Auth provider.
 -  *changePassword*, *sendPasswordResetLink* *resetPassword* moved to Password provider. Removed 
 *password* word from methods names (*change*, *sendResetLink*, *reset*).
 - Boards *getTitleSuggestionsFor* renamed to *titleSuggestionsFor*
 - Interests methods renamed: *getMain* to *main*, *getInfo* to *info*, *getPinsFor* to *pins*
 - Pins methods renamed: *getRelatedPins* to *related*, *userFeed* to *feed*
 - Topics methods renamed: *getInfo* to *info*, *getPinsFor* to *pins*
 - User methods renamed: *getUserName* to *username*
 - Method *getHttpClient* renamed to *getHttpClient*
 - Pinners *block* method by default uses username instead of id and returns boolean.
 
### Removed:
 - *comment* and *deleteComment* methods in Pins provider. Use Comments provider instead.
 
### Fixed:
 - Auto-login for blocked users 
 - *logout* method requires bot to be logged in

## v4.13.3 - 2016-10-25
### Fixed:
 - Pinterest Headers version
 - Pagination

## v4.13.2 - 2016-10-23
### Added:
 - Pins visual similar search via *visualSimilar*
 - Comments provider with *create* and *delete* methods
 - Pins *comment* and *deleteComment* methods are deprecated

### Removed:
 - News *last* method
 - AuthFailed exception
 
### Fixed:
 - 502 Pinterest response on request with no data

## v4.13.1 - 2016-10-15
### Fixed:
 - Fail on empty server response 

## v4.13.0 - 2016-10-09
### Added:
 - User *resetPassword* and *sendPasswordResetLink* methods to reset password.
 - HttpClient *parseCurrentUrl* method
 - HttpClient *setCookiesPath* method to change default store for cookie files.
 - User *invite* method to invite people by email.

## v4.12.7 - 2016-09-26
### Fixed:
 - Removed output cookie file path to console
 - Boards *info* method now returns only specified board

## v4.12.6 - 2016-09-26
### Fixed:
 - Creating cookie file if does not exist
 - User *register* default country is GB

### Changed:
 - *reload* param added to Bot *getClientContext* method.

## v4.12.5 - 2016-09-25
### Fixed:
 - Bot *getLastError* method.
 - Random cookie file name for non-authorized sessions.

## v4.12.4 - 2016-09-25
### Fixed:
 - Bot *getClientInfo* method with auto login.

## v4.12.3 - 2016-09-25
### Added:
- HttpClient *cookie* and *cookies* methods.
- Auto-login from a previously saved session.

### Fixed:
 - HttpClient *setOptions* does not override default Curl options, only adds additional ones.

### Removed:
 - HttpClient *setUserAgent* method, custom userAgent should be passed with 
 other Curl options via *setOptions* method.

## v4.11.2 - 2016-09-18
### Fixed:
 - Images upload

## v4.11.1 - 2016-09-18
### Added: 
 - Bot *getClientInfo* method.

## v4.11.0 - 2016-09-16
### Added:
 - User *changePassword*, *deactivate* methods.
 - AuthRequired exception.
 - Pins *related* method.
 - Pinners *likes* method.
 - News *all* method.
 - Boards *getTitleSuggestionsFor* method.
 
### Changed:
 - User *login* method does not throw an exception, now it returns *false* on fail.
 - Bot *getLastError* method returns string, not array.
 - News *last* method is deprecated.

## v4.10.1 - 2016-09-13
### Fixed:
 - Checks in *username* and *isBanned* methods

## v4.10.0 - 2016-09-12
### Added
 - User *isBanned* and *username* methods

## v4.9.0 - 2016-09-11
### Added:
 - Get user profile info

## v4.8.0 - 2016-09-11
### Added:
 - Get following boards/people/interests for a pinner

## v4.7.0 - 2016-09-10
### Added:
 - New Topics provider
### Changed:
 - Follow/unfollow methods moved from Interest to Topics provider
 - Added getRelatedTopics to Interests provider

## v4.6.1 - 2016-09-08
### Changed:
 - Provider::execGetRequest method now returns bool or array
 - Exceptions classes renamed
 - Helpers classes renamed
 - Removed HasFollowers trait
### Fixed:
 - Fixed *getUnFollowUrl()* method in Followable trait

## v4.6.0 - 2016-08-26
### Changed:
 - HttpClient cookie file name moved to property instead of constant
 - Custom curl options in HttpClient object
 - Removed deprecated methods (isLoggedIn, logout, login) from Bot class. Instead use User provider
 - Removed getRequest method from ProvidersContainer
 
## v4.5.4 - 2016-08-26
### Changed:
 - Http contract renamed to HttpClient
 - Curl specific methods moved to CurlHttpClient
 - Removed token logic from HttpClient, only parsing
 - Removed user agent logic from Request to HttpClient

### Fixed:
- Upload problems

## v4.5.3 - 2016-08-16
 - Fixed upload

## v4.5.2 - 2016-08-16

### Changed:
 - Renamed ProviderLoginCheckWrapper to ProviderWrapper
 - Refactored Response class
 - HttpInterface contract renamed to Http
 - HttpInterface contract renamed to HttpClient
 - PaginatedResponse contract added

## v4.5.1 - 2016-08-09

### Fixed:
 - Pins method *feed* required login

## v4.5.0 - 2016-08-09

### Added:
 - Interests: execGet main categories
 - Interests: execGet category info
 - Pins: execGet user feed

## v4.4.2 - 2016-07-06

### Fixed: 
 - Pins *comment* method

## v4.4.1 - 2016-07-02

### Fixed:
 - Pins *create* method uploads images.

## v4.4.0 - 2016-07-02

### Added:
 - User *registerBusiness* for registration business accounts.
 - User *convertToBusiness* to convert simple account to a business one.

## v4.3.0 - 2016-06-27

### Added:
 - User *register* method
 
### Fixed: 
 - Pins *edit* method returns bool value
  
### Changed:
 - *login*, *isLoggedIn* and *logout* methods move to User provider
 - Bot *login*, *isLoggedIn* and *logout* methods are deprecated
 - removed *RequestInterface* and *ResponseInterface*
 

## v4.2.2 - 2016-06-19
### Fixed:
 - keywords *recommendedFor* method returns an empty array if no results
 - Response *hasErrors* method returns true on errors
 - php version downgraded for 5.5.9
 - test changed for phpunit 4.0
 - Response *getBookmarks* method return empty array for no bookmarks

## v4.2.1 - 2016-06-14
### Fixed:
 - news *last* method login requirement check
 - pagination empty result check

## v4.2.0 - 2016-06-13
### Changed: 
 - functions with pagination accept a limit as a second argument, for example:
 ```php
 $bot->pins->search('cats', 2)
 ```
 will return only two pins for the search results. 
 The same is true about getting followers/following/pins for the user or board and getting pins from source.
  
 - *activity* method in pins provider (requires login).
	 

## v4.1.0 - 2016-06-12
### Added:
 - bot logout method

## v4.0.0 - 2016-06-12
### Changed:
 - generator objects now return an entity for each iteration, not an array of entities. For example, to 
 execGet search results there is no more need to make two nested loops:
 
 ```php
 foreach($bot->pins->search('cats') as $pin) {
 	echo $pin['id'], "\n";
 	// ...
 }
 
 The same is true about getting followers/following/pins for the user or board and getting pins from source. 

## v3.3.1 - 2016-06-12
### Added:
 - isLoggedIn method
 - Response and Request refactoring
 - Better exception messages in ProviderLoginCheckWrapper

## v3.2.3 - 2016-06-09
### Fixed:
 - Pins like/dislike
 - Response error check

## v3.2.2 - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns terms and their positions.

## v3.2.1 - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns items concatenated with the query request.

## v3.2.0 - 2016-06-04
### Added:
 - Keywords provider
 	- recommendedFor($query) method.

## v3.1.0 - 2016-06-04
### Added:
 - Change board (title, description, category and privacy)

## v3.0.0 - 2016-05-29
### Changed:
 - News *latest* method renamed to *last*
 - Request and providers refactoring
 
### Fixed:
 - Boards follow/unfollow request

## v2.6.0 - 2016-05-28
### Updated:
 - Pagination refactoring
 - Requests refactoring
 - version up due to the previous features

## v2.5.3 - 2016-05-28
### Added: 
- Edit profile image
- Get pins from specific source

## v2.5.2 - 2016-05-23
### Added: 
- Edit pin by id (change description, link and board)
- Move pin to a new board

## v2.5.1 - 2016-04-22
### Fixed:
- Cookie file now is placed in os PHP temporary directory

## v2.5.0 - 2016-03-12
### Added:
- Specify link when creating pin as third argument

## v2.4.2 - 2016-03-03
### Fixed:
- Login requirements check for some methods

## v2.4.1 - 2016-03-01
### Fixed:
- sendMessage, sendMail array bugs fix

## v2.4.0 - 2016-03-01
### Added:
1. Changes in sendMessage:
	- Many users at once
	- Add pin to message
2. Added sendMail method. Works like sendMessage, but pass email or array of emails instead of users ids.
