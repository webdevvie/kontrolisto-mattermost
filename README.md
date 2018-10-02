README
======

A simple tool that takes text data from stdin and sends it to the mattermost channel of your choosing provided it is configured. Check out mattermost.yml.dist for an example of how to configure it.

Written by John Bakker

https://github.com/webdevvie/kontrolisto-mattermost

You can send either just send text or send in json to override any of the channel settings in the config.
Everything except for the actual hook can be changed.

Example:
`echo "Hello world" | ./mattermost.phar send default`

Example:
`echo "{\"text\":\"Your text here\",\"name\":"coolstuff"}" | ./mattermost.phar send default`

Your config file should be in the same directory as the phar file and named:
mattermost.yml
or if you changed the phar file's name :
yourfilename.yml

The tool is used by Kontrolisto to send updates to Mattermost channels.
But can quite easily be used in/for other tools.

Use webdevvie/pakket to build a phar out of this and use package.sh to package it.