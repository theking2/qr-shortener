# qr-shortener

# DB - setup
The main table for code/url translation

## table and view
```
create table `code` (
  `code` char(5) character set ascii collate ascii_bin not null,
  `url` varchar(240) character set ascii collate ascii_bin default null,
  `last_used` datetime not null default '0000-00-00 00:00:00',
  `hits` int(10) unsigned not null default 0,
  primary key (`code`) using hash,
  key `url` (`url`) using hash
) engine=myisam default charset=latin1 collate=latin1_bin pack_keys=0 row_format=redundant;
```
To populate this table run `make-shorts.php`
View to get used and total number of codes:
```
create view `used` as
select
  count(`code`.`url`) AS `urls`
, count(0) AS `total` from `code`
```

## routines
Routine to get a url based on the code, updating hit and last_used.
```
create function `get_url`(`c` char(5)) returns varchar(255) charset utf8
begin
  declare result varchar(255);
  update code
	set
    hits = hits + 1
  , last_used=current_timestamp()
  where code = c;

  return (
		select url
    from code
    where code = c
	);
end ;;
```

routine to reserve a code for a url
```
create function `set_url`(`the_url` varchar(255)) returns char(5) charset ascii
begin
	declare result char(5);

  select `code` into result from `code` where url = the_url limit 1;
  if result is null then
    select `code` into result from `code` where url is null limit 1;
	  update `code`
    set
      url=the_url
    where `code` = result;
	end if;
  return result;
end ;;
```



