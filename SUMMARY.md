# Kromin Italian Winery APIs test

## What is this test about?
This laravel application mostly works but the architecture and code quality are questionable.

In some parts the code is just a bit wonky and needs some refactoring, in others it seems like the requests have been misinterpreted.

## Application summary
These REST APIs allow an Italian winery to showcase online all their most prestigious bottles of wine.

Users can check the bottles out through an URL and Admins can edit the informations to showcase.

## Requests

1) It seems like the Photo APIs are following some weird routing structure, could you fix it to make it follow the REST guidelines?
2) In order to view a wine, the users should follow a link with a unique uiid in order to see the bottle. How would you fix them?
3) All these DB::table queries seem out of place considering we use models, also a lot of them are repeated. Can you refactor them in a way that would follow the DRY principle?
