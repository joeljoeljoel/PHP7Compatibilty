```
composer install
php list-repos.php | uniq | sort > all-repos.txt
sh checkout-sites.sh all-repos.txt
php check.php
```