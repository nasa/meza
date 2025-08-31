## Meza Release Notes 43.29.1 → HEAD

### Commits

HEAD -> main 
## Meza 43.33.1 origin/main origin/HEAD
* [f5159bda](https://github.com/freephile/meza/commit/f5159bda) (2025-08-31) Greg Rundlett: Add DynamicPageList3 Extension DPL allows for creating lists of pages like you would be able to
using SMW queries and properties, but with simpler mechanics and
a small learning curve.
We've added the actively maintained DynamicPageList3 and will switch
to DynamicPageList4 soon.
Fixes Issue [#198](https://github.com/freephile/meza/issues/198)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.32.1
* [c0a5192e](https://github.com/freephile/meza/commit/c0a5192e) (2025-08-29) Greg Rundlett: Use MediaWiki REL1_43 and  SMW 6.x With fixes in the latest MediaWiki and SMW 6, everything appears
to be working again and we are not restricted to MW 1.43.1
See https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki_6.0.0
https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki_6.0.1
Fixes Issue [#163](https://github.com/freephile/meza/issues/163)
Adds Feature #185
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/defaults.yml`

## Meza 43.31.4
* [bf35f37e](https://github.com/freephile/meza/commit/bf35f37e) (2025-08-28) Greg Rundlett: Do not ignore errors in memcache installation The PECL installation of the PHP-memcache extension was brute force.
Therefore it would error every deploy once it was installed. And so
'ignore errors' was on.
Instead, now we look to see whether it is installed;
register a fact memcached_pecl_installed when it is already present.
And thus skip installation depending on the circumstance.
Partly addresses Issue [#144](https://github.com/freephile/meza/issues/144)
  - Modified: `src/roles/apache-php/tasks/php-redhat8.yml`

origin/revise-hosts-template revise-hosts-template
* [ddac1217](https://github.com/freephile/meza/commit/ddac1217) (2025-08-28) Greg Rundlett: Use YAML instead of INI in hosts file YAML is preferred for Ansible, and is more suitable for more complex
hosts management.
See Issue [#68](https://github.com/freephile/meza/issues/68)
  - Modified: `src/roles/setup-env/templates/hosts.j2`

## Meza 43.31.3
* [72adcda9](https://github.com/freephile/meza/commit/72adcda9) (2025-08-28) Greg Rundlett: Fix deprecation warning: collections_path Settings variable name is singular, not plural.
[DEPRECATION WARNING]: [defaults]collections_paths option, does not
fit var naming standard, use the singular form collections_path
instead. This feature will be removed from ansible-core in version 2.19.
Deprecation warnings can be disabled by setting
deprecation_warnings=False in ansible.cfg.
Also, add symlink to config/ansible.cfg from project root because some
tools or IDEs expect it there.
  - Added: `ansible.cfg`
  - Modified: `config/ansible.cfg`

## Meza 43.31.2
* [190e7f30](https://github.com/freephile/meza/commit/190e7f30) (2025-08-28) Greg Rundlett: Fix branch name for Medik skin This repo uses 'master' not 'main' terminology
Fixes #139
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.31.1
* [e7b2ea3c](https://github.com/freephile/meza/commit/e7b2ea3c) (2025-08-27) Greg Rundlett: Re-enable the Medik Skin By enabling the main branch we should pull in the proper commits for
compatibility with 1.43 and 1.44 too
Do not use the tag 5.1.3
Fixes #139
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.30.2
* [65bc6c43](https://github.com/freephile/meza/commit/65bc6c43) (2025-08-27) Greg Rundlett: Regenerate RELEASE_NOTES up to 43.25.11 Use src/scripts/generate-release-notes.sh to
update for additional links formatting and --name-status info.
  - Modified: `RELEASE_NOTES-43.25.11.md`

## Meza 43.30.1
* [53cc7484](https://github.com/freephile/meza/commit/53cc7484) (2025-08-27) Greg Rundlett: Add script to generate RELEASE NOTES Also update the latest release notes.
  - Modified: `RELEASE_NOTES-43.29.1.md`
  - Added: `src/scripts/generate-release-notes.sh`

## Meza 43.29.2
* [148ea866](https://github.com/freephile/meza/commit/148ea866) (2025-08-26) Greg Rundlett: Add latest RELEASE_NOTES Updated procedure at https://wiki.freephile.org/wiki/RELEASE_NOTES
  - Added: `RELEASE_NOTES-43.29.1.md`
