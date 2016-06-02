#Fantasticsimport

#instructions

1) Export node list

- ie [exports/2016/03-05/getNodeList.sql](exports/2016/03-05/getNodeList.sql)

2) From that list, create export script with macros or [Sublime](https://www.sublimetext.com/) shortcuts

- ie [exports/2016/03-05/exportAll.sh](exports/2016/03-05/exportAll.sh)

3) Create symbolic link in Drupal 6 installation of fantasticsmag to the export folder

4) Make sure [drush](http://docs.drush.org/en/master/install/) is installed. (Use Drush 7.x for Drupal 6 install)

5) Make sure [Node Export](https://www.drupal.org/project/node_export). Make sure you view all releases and install the 6.x version

6) Run the export script

- ie [exports/2016/03-05/exportAll.sh](exports/2016/03-05/exportAll.sh)

7) Once the export script completes, create the import script

- ie [exports/2016/03-05/importAll.sh](exports/2016/03-05/importAll.sh)

8) Make sure [WP-CLI](https://wp-cli.org/) is installed in your wordpress install.

9) Run the import script from the wp-content/plugins/fantasticsimport folder (use `vagrant ssh` if using [Vagrant](https://www.vagrantup.com/))

- ie [exports/2016/03-05/importAll.sh](exports/2016/03-05/importAll.sh)

10) TODO: include fixes for filenames with spaces

11) TODO: include fix for permalinks (ie `stories/storiesexample`)