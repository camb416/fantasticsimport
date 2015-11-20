#!/bin/bash
echo "fixing stories... (this takes a while)"
wp search-replace '^stories' '' fm_posts --regex
echo "fixing covers... (this takes a while)"
wp search-replace '^covers' '' fm_posts --regex
echo "Done."