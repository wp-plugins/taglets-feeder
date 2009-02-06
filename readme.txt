=== Taglets Feeder ===
Contributors: taglets
Tags: taglets, tags
Requires at least: 2.5
Tested up to: 2.7
Stable tag: trunk

Taglets Feeder is a Wordpress plug-in that announces your blog postings on Taglets.org when you publish a post.

== Description ==

Taglets Feeder is a Wordpress plug-in that announces your blog postings on Taglets.org when you publish a post.

For each tag you specify for the post in Wordpress during post creation, when you publish the post, Taglets Feeder sends a comment to those tags with a comment of the form: [post title] [short url]

For example:

    tag:  mrblog
    comment: Taglets Feeder WP plugin now available on Wordpress.org http://shortna.me/d79fb

You can optionally specify a tag that Taglets Feeder always sends to, for every post you publish (e.g. a tag for the name of your blog), in addition to the tags specified for the post.

You can also specify a excluded catagory such that for posts assigned to that cateofy, Taglets Feeder will not notify Taglets.org.

The normal behavior of Taglets Feeder is to attempt to post to all tags (send notifications for all tags) and ignore errors for tags that do not exist on Taglets.org.  If you prefer, you can use the "auto-create" feature of Taglets Feeder to cause it to dynamically create any non-existent tags on the fly.  If you activate the "auto-create" option in Taglets Feeder Settings, you must also enter your Taglets.org email and password so that Taglets Feeder can craeet the tag on your behalf.

== Installation ==

1. Extract taglets-feeder.zip to /wp-content/plugins/taglets-feeder/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Setup your fixed tag (if any) and other options on the options page.
4. Publish a post!
5. See taglets.org tags updated.

== Frequently Asked Questions ==

== Screenshots ==

1. Taglets Feeder Settings
