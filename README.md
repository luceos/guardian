<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a>

# guardian

Guardian is an extension for Flarum that automates reviewing users based on their forum behaviour and activity.

# How it works

## Footprinting

In order to understand whether a user has bad intent, we keep track 
of a few key actions. We do this by listening to existing events. These events
can come from Flarum or (possibly installed) extensions. Listening to events that
are not being fired is not an issue here.

The process of listening to events is called footprinting. For footprinting we use 
a configuration of events and a relative configuration to act on them.

Guardian looks at a configuration file the first it finds from:

- storage/guardian/events.yaml
- guardian.yaml
- vendor/<package>/resources/configs/events.yaml

The configuration contains a list of event entries, each entry has a:

- `name` giving a human readable explanation of the event
- `class` the Flarum event we're listening for and act upon.
- `score` either an integer (positive or negative) or a class string
- `user` a Laravel "dot" like value to indicate the what property refers to the Actor.

The [src/Listeners/FootPrinting](/src/Listeners/FootPrinting.php) class takes care of listening
to the events from the configuration. Aside from identifying the actor it will fire a FootPrinting event
which is being used to dynamically modify the score if so required by a Marker class.

Marker classes allow for more complex scoring during events. For instance it allows to calculate the
number of posts or discussions of an actor. An example of such a marker can be found in the [DiscussionDeleted one](src/Markers/DiscussionDeleted.php)
because you're impacted increasingly negatively in case your discussion was deleted with replies.

# License

This extension is made available under the [CC-BY-NC-SA 4.0](http://creativecommons.org/licenses/by-nc-sa/4.0/), meaning:

- You are allowed to use and fork this code.
- You cannot publish this extension under another license or keep it closed source.
- You cannot use it commercially.

This license is implemented to ensure we all work on the same code base in order to achieve the best result possible.
