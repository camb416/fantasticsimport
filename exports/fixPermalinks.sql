# UPDATE fm_posts
# SET post_name = REPLACE(post_name,'stories','')
# WHERE post_name LIKE 'stories%'
# AND post_type = "fmag_story";

UPDATE fm_posts
SET post_name = REPLACE(post_name,'covers','')
WHERE post_name LIKE "covers%"
AND post_type = "fmag_cover";