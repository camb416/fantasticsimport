#!/bin/bash
# fix a bug in old imports
wp search-replace '<!-- more -->' '<!--more-->' fm_posts
