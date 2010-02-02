Renowned Media Search API

Each file ending in '.php' in the /includes/search/ directory is loaded and used for the site search.

They are loaded alphabetically and the filename doesn't really matter, so if you prefer one thing to be
ordered before another thing, a good practice would be to prepend the filename with a number. e.g.:

1textual.ssi.php
2articles.ssi.php

If you don't want a particular item to be indexed, rename it or delete it so that it no longer contains '.php'.

1textual.ssi.php
2articles.txt

The variable $search contains the search term the user is looking for. The array $result_data should contain
the following items by the time the file is finished:

$result_data['count'] = the number of items that were found matching $search.
$result_data['title'] = the title of the search, e.g. 'Found 27 matching articles'

The array $results should contain the search results in the following format:

$results[i]['title'] = Link text of found item (Title)
$results[i]['url'] = Link URL to indexed item. This should be relative (if searching our site) but absolute would work too.
$results[i]['content'] = Summary text of searched item. search_highlight() will highlight items that match $search.