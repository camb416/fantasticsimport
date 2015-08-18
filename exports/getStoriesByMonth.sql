SELECT * FROM node
WHERE type = 'fmag_story'
AND FROM_UNIXTIME(created,"%m,%Y") = "01,2007";

