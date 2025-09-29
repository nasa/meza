## Meza Release Notes 43.29.1 → HEAD

### Commits

HEAD -> dev
* [5068855d](https://github.com/freephile/meza/commit/5068855d) (2025-09-25) Greg Rundlett: Fix lint errors on essential-vars task file Also add fully qualified collection names (FQCN) to avoid other lint
errors. e.g. `ansible.builtin.set_fact` instead of just `set_fact`.
  - Modified: `src/roles/essential-vars/tasks/main.yml`

## Meza 43.39.2 origin/fix-207-essential-vars
* [b65ba2df](https://github.com/freephile/meza/commit/b65ba2df) (2025-09-25) Greg Rundlett: Fixup the test-certbot playbook to use set-vars The test-certbot playbook was broken - referencing an unknown role.
And it did not use the `set-vars` role to properly initialize.
Now it uses `set-vars` and includes the local certbot role.
Adds to the fix for Issue [#207](https://github.com/freephile/meza/issues/207)
  - Modified: `src/playbooks/test-certbot.yml`

## Meza 43.39.1
* [ab879a58](https://github.com/freephile/meza/commit/ab879a58) (2025-09-25) Greg Rundlett: Setup key variables early; avoid extra-vars We're one step closer to not needing sudo in the deploy command.
With a new role 'essential-vars', we set `ansible_user` and
`group_wheel` with OS-specific logic.
The variables `group_wheel` and `ansible_user` will now be consistently
available throughout all playbooks and roles where they are referenced.
On a RedHat family OS, using 'sudo meza deploy...' we get:
['group_wheel: wheel', 'ansible_user: meza-ansible', 'ansible_env.USER: root']
- Updated the bootstrap section of site.yml to use the new role
  instead of inline variable definitions.
- Maintains compatibility with existing `set-vars` role structure.
- Replace usage of `owner: "{{ ansible_user | default(ansible_env.USER) }}"`
  with owner: "{{ ansible_user | default('meza-ansible') }}"
  because when using 'sudo deploy...' $USER evaluates to 'root'!
- Use 'become' for operations in access-restricted directories where
  restrictions can otherwise prevent success. This is especially
  important for SSH private keys which must maintain strict security
  permissions (`mode: 0600`) and proper ownership to function correctly.
  Fixed the `mediawiki` role SSH key copy tasks and the `site.yml`
  bootstrap section SSH key copy tasks.
- On paths that belong to 'meza-ansible' (like ~/.ssh), set the owner
  explicitly instead of using a variable that could evaluate to
  something incorrect.
- These fixes ammend earlier attempts to fix permission issues like
  commit eee92a08 which used 'default(ansible_env.USER)' instead of
  the correct "default('meza-ansible')"
- Update CONTRIBUTING.md with info on establishing interactive login
  as meza-ansible
- TODO: Skip copy id_rsa to id_rsa and similar copy operations when
  `inventory_hostname == 'localhost'`
Fixes Issue [#207](https://github.com/freephile/meza/issues/207)
  - Modified: `CONTRIBUTING.md`
  - Modified: `config/paths.yml`
  - Modified: `src/playbooks/site.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/tasks/main.meza.yml`
  - Modified: `src/roles/base/tasks/main.yml`
  - Modified: `src/roles/cron/tasks/main.yml`
  - Modified: `src/roles/database/tasks/secure-installation.yml`
  - Added: `src/roles/essential-vars/tasks/main.yml`
  - Modified: `src/roles/init-controller-config/tasks/main.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`
  - Modified: `src/roles/meza-log/tasks/main.yml`
  - Modified: `src/roles/saml/tasks/main.yml`

## Meza 43.38.3
* [74851065](https://github.com/freephile/meza/commit/74851065) (2025-09-25) Greg Rundlett: Add FIXME comment about CommentStreams not working CommentStreams is not showing any errors, nor functionality in our tests
of Meza 43.x
See https://phabricator.wikimedia.org/T388462
and the extension page for updates
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.38.2
* [c35a73bb](https://github.com/freephile/meza/commit/c35a73bb) (2025-09-25) Greg Rundlett: Add comments to CommentStreams config Meza specifically sets
      $wgCommentStreamsAllowedNamespaces = -1;
so you MUST use <comment-streams /> in the page content to enable it
on a per page basis.
If left to the default (null), comment-streams is allowed on all
pages in all content namespaces.
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.38.1
* [bf74c315](https://github.com/freephile/meza/commit/bf74c315) (2025-09-23) Rich Evans: Update MezaCoreExtensions.yml Update Extension:CommentStreams to use "mediawiki_default_branch" rather than "master"
  - Modified: `config/MezaCoreExtensions.yml`

* [40337063](https://github.com/freephile/meza/commit/40337063) (2025-09-17) Greg Rundlett: Add a new script to update the CHANGELOG updateCHANGELOG.sh simply uses git log --pretty to prepend new git activity into the CHANGELOG The CHANGELOG is a 'raw' version of what is changed.
The RELEASE_NOTES are the more stylized content where the output is
formatted in markdown, links are included for issues and commit SHAs.
  - Modified: `CHANGELOG`
  - Added: `src/scripts/updateCHANGELOG.sh`

## Meza 43.37.3
* [5e50315a](https://github.com/freephile/meza/commit/5e50315a) (2025-09-16) Greg Rundlett: Update CONTRIBUTING.md with new details Begin the process of updating the tools and quality controls for
Meza.
  - Modified: `CONTRIBUTING.md`

## Meza 43.37.2
* [a23b1bdf](https://github.com/freephile/meza/commit/a23b1bdf) (2025-09-15) Greg Rundlett: Add comment for extension UrlGetParameters As the name implies, the extension enables you to use and/or display
the "GET" parameters of the requested URL.
https://www.mediawiki.org/wiki/Extension:UrlGetParameters
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.37.1
* [f041dc20](https://github.com/freephile/meza/commit/f041dc20) (2025-09-15) Greg Rundlett: Add config and UPO for HTML emails by default Allowing HTML email only enables the User Preference Option.
$wgAllowHTMLEmail = true;
And, we set the UPO over-riding the default of 'plain-text'
$wgDefaultUserOptions['echo-email-format'] = 'html';
  - Modified: `src/roles/mediawiki/templates/LocalSettings.php.j2`

## Meza 43.36.2
* [07ebd3df](https://github.com/freephile/meza/commit/07ebd3df) (2025-09-11) Greg Rundlett: Update Meza Core Skins - Delete duplicate MinervaNeue block
- Add comment for Timeless skin
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.36.1
* [eee92a08](https://github.com/freephile/meza/commit/eee92a08) (2025-09-11) Greg Rundlett: Ensure the verify-permissions playbook executes Add pre_tasks section to the playbook to load variables via the set-vars role.
All playbooks should do this.
Eliminate specification of owner/group in the test for cache writing by meza-ansible:
- owner: "{{ ansible_user | default(ansible_env.USER) }}"
- group: "{{ group_apache }}"
because those cause a chmod which is not allowed even though meza-ansible
can write to the cache directory.
Note: although we could get a full shell with
become_flags: '-i'
this is not necessary for testing write permissions in the cache directory
Ensure message display by filtering items to integer with the 'int' jinja filter
Final work following on https://github.com/freephile/meza/commit/b3badfcf6ffe518115c83271e03e0028b589eed9
Fixes Issue [#186](https://github.com/freephile/meza/issues/186)
  - Modified: `src/playbooks/verify-permissions.yml`
  - Modified: `src/roles/verify-permissions/tasks/main.yml`

## Meza 43.35.1
* [138d1092](https://github.com/freephile/meza/commit/138d1092) (2025-09-06) Greg Rundlett: Add missing Bundled extensions There were 7 bundled extensions which were in MezaLocalExtensions.yml
instead of the MezaCoreExtensions.yml - thus not included in the Meza distribution.
Adds
- CategoryTree
- CheckUser
- CiteThisPage
- ConfirmEdit
- Nuke
- Poem
- TemplateStyles
Fixes Issue [#201](https://github.com/freephile/meza/issues/201)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.34.1
* [7335503c](https://github.com/freephile/meza/commit/7335503c) (2025-09-04) Greg Rundlett: Update PageForms to 6.x Switch to using Composer for PageForms to make version constraints
easier to manage.
Also quote the version "master" for SemanticDependencyUpdater
Fixes Issue [#200](https://github.com/freephile/meza/issues/200) exception from SMWDIProperty class not found
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.33.2
* [6b9c3765](https://github.com/freephile/meza/commit/6b9c3765) (2025-08-31) Greg Rundlett: Add additional RELEASE_NOTES file up to HEAD This RELEASE_NOTES can be updated throughout a release cycle until
such time that a release cycle ends when it can be saved off as the
full historical RELEASE NOTES for that cycle.
To generate updates to this file during the 43.x release cycle,
use .src/scripts/generate-release-notes.sh 43.29.1 HEAD
since 43.29.1 was the last ref used in
https://github.com/freephile/meza/blob/main/RELEASE_NOTES-43.29.1.md
Partly addresses Issue [#3](https://github.com/freephile/meza/issues/3)
  - Added: `RELEASE_NOTES-HEAD.md`

## Meza 43.33.1
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

* [53dcb430](https://github.com/freephile/meza/commit/53dcb430) (2025-06-03) Rich Evans: Update MezaCoreExtensions.yml Set Page Forms Version to the last version that supports MWM 1.39 (tag 5.9.1)
  - Modified: `config/MezaCoreExtensions.yml`

nasa/amcgillivray-nasa-patch-1
* [28a721c7](https://github.com/freephile/meza/commit/28a721c7) (2025-03-26) amcgillivray-nasa: Update MezaCoreExtensions.yml Granted the DeleteBatch permission to sysops (previous configuration did not grant the right to any groups)
  - Modified: `config/MezaCoreExtensions.yml`

* [a4e8bdf0](https://github.com/freephile/meza/commit/a4e8bdf0) (2024-12-19) Rich Evans: Update haproxy.cfg.j2 removed commented-out obsolete configuration text in haproxy.cfg
  - Modified: `src/roles/haproxy/templates/haproxy.cfg.j2`

nasa/getMeza-patch-1
* [4f341436](https://github.com/freephile/meza/commit/4f341436) (2024-12-02) amcgillivray-nasa: Update getmeza.sh Incorporates cowen23's fix for Issue [#57](https://github.com/freephile/meza/issues/57)
  - Modified: `src/scripts/getmeza.sh`

* [ebcb21cc](https://github.com/freephile/meza/commit/ebcb21cc) (2024-09-18) Rich Evans: Update MezaCoreExtensions.yml added Extension UrlGetParameters
  - Modified: `config/MezaCoreExtensions.yml`

* [d15eabdb](https://github.com/freephile/meza/commit/d15eabdb) (2024-09-18) Rich Evans: Update MezaCoreExtensions.yml Update SMW config to not use links in values
  - Modified: `config/MezaCoreExtensions.yml`
