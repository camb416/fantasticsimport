SELECT m.meta_value FROM fm_posts p, fm_postmeta m WHERE 
(p.post_type = "fmag_story" OR p.post_type = "fmag_cover")
AND
m.meta_key = "legacy_id" AND
p.id = m.post_id 
AND YEAR(p.`post_date`) = 2010;
