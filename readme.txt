=== Paid Memberships Pro: Downloads Add On ===
Contributors: strangerstudios, dparker1005
Tags: pmpro, paid memberships pro, members, memberships, downloads, files, restricted
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create restricted file downloads for members.

== Description ==

Create and manage restricted file downloads for your membership site. Upload files to a secure, non-public directory and control access based on membership level. Members see a download link; non-members see which level is required and a prompt to join.

Each download is a custom post type with a title, description, and uploaded file. Restrict access using the standard PMPro "Require Membership" panel. Files are stored in the PMPro restricted files directory, protected from direct access.

Display downloads using three templates (link, card, or button) via shortcodes or Gutenberg blocks with live editor previews. The Download Library block and `[pmpro_download_library]` shortcode display multiple downloads in list or grid layouts.

= How It Works =

This Add On registers a Download custom post type. Each Download post holds a single protected file stored in PMPro's restricted files directory — outside of the public WordPress uploads folder.

When a visitor requests a download, PMPro checks their membership level before serving the file. If they don't have the right level, they are shown a locked state with a call-to-action to join or upgrade.

= Displaying Downloads =

Two blocks are available in the block inserter under the PMPro category:

* **PMPro Download** — displays a single download by ID.
* **PMPro Download Library** — displays all published downloads in a list or grid, with an option to filter by download category.

Two shortcodes are also available:

* `[pmpro_download id="123" template="link" label="title"]` — displays a single download.
* `[pmpro_download_library template="link" layout="list" columns="2" category="slug-1,slug-2"]` — displays all published downloads, optionally filtered by download category (comma-separated term slugs or IDs).

= Display Templates =

* **Link** — A compact text link with a file-type icon. Locked downloads show a lock icon and the required membership level.
* **Card** — A PMPro-styled card with the download title, optional description, and an action button. Locked downloads show the membership requirement and a "Join Now" or "View Membership Options" button.
* **Button** — A prominent CTA button with a download icon. Locked downloads link to the checkout or levels page.

== Installation ==

1. Navigate to Memberships > Add Ons in the WordPress admin.
1. Locate the Downloads Add On and click Install Now.
1. Activate the plugin through the Plugins screen in the WordPress admin.
1. Navigate to Downloads in the WordPress admin sidebar and click Add New Download.
1. Enter a Download Name, upload a file, and set membership restrictions.
1. Use the `[pmpro_download]` shortcode or the PMPro Download block to display a single download on any page.
1. Use the `[pmpro_download_library]` shortcode or the PMPro Download Library block to display multiple downloads.

[View full documentation](https://www.paidmembershipspro.com/add-ons/pmpro-downloads/)

== Frequently Asked Questions ==

= Where are my uploaded files stored? =

Files are stored in PMPro's restricted uploads directory, which is located outside of the standard WordPress `wp-content/uploads` folder and is protected from direct browser access. Files can only be served through PMPro's authenticated download handler.

= What file types can I upload? =

Any file type that WordPress permits for upload on your site. The allowed file types are controlled by WordPress's standard `upload_mimes` filter.

= Can a member download a file multiple times? =

Yes. There is no download limit per user. Once a member has the correct membership level, they can download the file as many times as they like.

= What happens when I delete a Download post? =

The physical file is permanently deleted from the server when the post is permanently deleted (not just moved to the Trash).

= What happens when I replace a file? =

The old file is deleted from the server only after the new file uploads successfully. If the new upload fails, the original file is preserved.

= Can I display multiple downloads on one page? =

Yes, use the `[pmpro_download_library]` shortcode or block to show all downloads, or place multiple `[pmpro_download id="..."]` shortcodes on the same page.

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. [https://github.com/strangerstudios/pmpro-downloads/issues/new/choose](https://github.com/strangerstudios/pmpro-downloads/issues/new/choose)

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at [https://www.paidmembershipspro.com](https://www.paidmembershipspro.com) for more documentation and our support forums.

== Changelog ==
= 1.2 - 2026-07-20 =
* FEATURE: Added Download Categories, allowing downloads to be organized and categories to be protected by membership level. #7 (@kimcoleman)
* ENHANCEMENT: Forced Downloads to use the block editor when the Classic Editor plugin is active, ensuring the download editing interface remains available. #8 (@kimcoleman)
* BUG FIX: Fixed locked download templates to hide membership levels that do not allow signups and show generic messaging when no public levels are available. #6 (@kimcoleman)

= 1.1 - 2026-05-04 =
* ENHANCEMENT: Added a new "Embed Image" toggle to the PMPro Download block (and `embed_image="true"` shortcode attribute) that displays image-type downloads inline for members with access. #4 (@dparker1005)
* BUG FIX/ENHANCEMENT: The `[pmpro_download]` shortcode placed in a level confirmation message now renders as a clickable download link when the confirmation message is included in checkout emails, instead of appearing as raw shortcode text. Invalid shortcodes are left unchanged so admins can spot the typo. #3 (@dparker1005)

= 1.0 - 2026-03-30 =
* Initial release.
