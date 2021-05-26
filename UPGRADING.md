# Upgrade Guide

*Always run the latest migrations after upgrading.*

## Version 1 to 2
***

This was a major refactor, focusing on simplifying the library and splitting out the
storage/retrieval aspect to `SettingModel`. The service and the library's getters and setters
remain the same so most code should not need updating, but be aware of the following points:

* Instead of a session key provide an implementation of `codeigniter4/authentication-implementation` for determining the current user (see [User Guide](https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html)).
* Corollary, the library assumes the required function is pre-loaded (e.g. call `helper('auth')` before using `Settings`).
* The `scope` field has been removed (though remains temporarily in the database)
* The `protected` field now handles `scope`'s original role: determining whether a Setting is "global" or can be overridden by a user value.
* The config file is stripped of unused properties - update to the latest (see **examples/**)
* Module Exceptions have been removed, do not try to catch them
* An existing template is no longer a requirement for a Setting (see README on "Dynamic Settings")

Note: A few database fields were incorrect (missing defaults, incorrect `null`).
The migrations [have already been updated](https://github.com/tattersoftware/codeigniter4-settings/commit/c04e9786a4f63f201a96dd493a56906217f3e471#diff-63d471d44e7b8969aa5fdb0d55653d9cc25a86f18e01aafae08172b1d7f20058)
to reflect their proper state. This only really affects testing so new migrations were not
generated, but current production instances should be aware of the nuanced differences.
