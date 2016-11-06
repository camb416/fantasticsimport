SELECT nid FROM node
WHERE type IN ('fmag_story','cover')
AND YEAR(FROM_UNIXTIME(created)) = 2016
AND MONTH(FROM_UNIXTIME(created)) >= 10
AND MONTH(FROM_UNIXTIME(created)) < 11;
