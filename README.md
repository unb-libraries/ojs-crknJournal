# OJS3 CRKN IP Subscriptions module and updater script

## Step 1 - Create database tables.

```
php etc/update.php --ojs /path/to/ojs --create 
```

## Step 2 - Update the table with the latest data.

```
php etc/update.php --ojs /path/to/ojs --data https://url.to/data.xml

```

## Step 3 - Enable the CRKN IP Subscriptions module for every journal that should grant access to those users.
