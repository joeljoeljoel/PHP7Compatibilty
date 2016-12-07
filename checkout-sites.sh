#!/bin/sh

while read site
do
    cd /Users/joel/Documents/git/sites/

    if [ ! -d "$site" ] ; then
        git clone git@github.com:viastudio/$site.git --quiet
        rc=$?

        if [[ $rc != 0 ]] ; then
            echo "Could not clone project: $site"
            continue
        fi
    fi

    cd $site
    git checkout master --quiet
    rc=$?

    if [[ $rc != 0 ]] ; then
        echo "Could not checkout master: $site"
        continue
    fi

    git pull origin master --quiet

    if [ ! -d wp-content ] ; then
        continue;
    fi

    echo $site;

done < "$1"
