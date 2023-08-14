## Setup

### src -> web -> login.php
update the path in do_oidc_login_redirect function to full path of the folder in the bb-dev server. Ex: "/kshah220su/lti_try/lti-1-3-php-example-tool/src/web/game.php"

### src -> web -> jwks.php
update kid to kid found in public keyset link provided by blackboard.
Ex link: https://developer.blackboard.com/.well-known/jwks.json

### src -> web -> jwks.json
update the json file with the json file found in blackboard jwks link.
Ex link: https://developer.blackboard.com/.well-known/jwks.json

### src -> vendor -> imsglobal -> lti-1p3-tool -> src -> LTI_Message_Launch.php
comment out the code in the validate_state() function and just return $this

### If making a deep linking tool, update 
#### src -> web -> game.php
update path in all three li items in is deep launch condition.
#### src -> web -> configure.php
update path in set_url function to full path of the folder on bb dev server


