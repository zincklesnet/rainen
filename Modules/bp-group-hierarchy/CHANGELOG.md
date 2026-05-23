# Changelog

All notable changes to BP Group Hierarchy will be documented in this file.

## [2.0.0] — 2026-04-14

### ✨ Major New Features

#### Group Tags System
- **Tag CRUD:** Groups can have up to 3 tags each, stored as serialized array in group meta (`bpgh_tags`).
- **Tag Search Widget:** Autocomplete-powered search box for finding groups by tag.
- **Tag Cloud Widget:** Weighted tag cloud with size scaling based on usage frequency.
- **Tag Moderation:** Optional admin-only approved tags list; toggle via settings.
- **Tag-based filtering** on group directory via `?bpgh_tag=slug` URL parameter.

#### Group Categories System
- **Admin-defined categories** stored as WP option (`bpgh_categories`) in `slug:Label` format.
- **Category assignment** per group via group meta (`bpgh_category`).
- **Category Filter Widget:** List or dropdown mode with counts and active state highlighting.
- **Category-based filtering** on group directory via `?bpgh_category=slug` URL parameter.
- **Category management** in admin settings (add/remove via textarea).

#### Role-Based Permissions
- **Parent group creation** restricted to admins by default (configurable).
- **Child group approval** workflow: non-admin child groups marked pending (`bpgh_pending_approval`).
- **Approve/reject** pending groups via AJAX admin actions.
- **Visibility inheritance:** child groups inherit parent's visibility scope.
- **Three visibility scopes:** `network`, `site`, `hidden` — stored as group meta.

#### Premium Tiers & ZCreds Integration
- **Free/Premium group tiers** via group meta (`bpgh_premium_tier`).
- **ZCreds upgrade:** myCred-powered point-based upgrade system.
- **Premium features:** Custom background image, animated image, video background, color schemes, typography.
- **Premium badge** displayed on group cards, tooltips, and directory.
- **Configurable upgrade cost** and myCred point type in admin settings.

#### Avatar-Styled Hover Tooltips
- **AJAX-powered tooltips** on group name hover showing: avatar, description, admins, member count, tags, category, premium badge.
- **Smart positioning** with viewport boundary detection.
- **Response caching** to minimize repeat AJAX calls.
- **Configurable content:** Toggle admins, tags, category display in tooltip via settings.
- **Dedicated CSS** for tooltip popup, header, meta sections, responsive layout.

#### BuddyPress Group Types Integration
- **Hierarchy-aware type filtering** with type filter tabs on group directory.
- **Admin columns** showing group type in BuddyPress group admin.
- **URL-based type filtering** via query injection.
- **Shortcode support** for type-filtered group lists.

#### Enhanced Group Directory
- **AJAX filtering** combining category + tag + group type + sort + parent filters.
- **Group cards** with avatar, description, member count, category badge, tags, premium badge.
- **Breadcrumbs:** `Parent → Child → Group` navigation on single group pages.
- **"Back to Parent Group"** and **"View Child Groups"** links.
- **Sorting** by name, date, activity, member count.
- **Pagination** with AJAX page loading.

#### 6 Shortcodes
- `[bpgh_parent_groups]` — All top-level parent groups with avatars, tooltips, sorting.
- `[bpgh_child_groups parent="123"]` — Child groups for a specific parent.
- `[bpgh_group_categories]` — Category listing with group counts.
- `[bpgh_group_tags]` — Tag cloud or list.
- `[bpgh_network_groups]` — Network-wide group listing (multisite).
- `[bpgh_group_directory]` — Enhanced full directory with filters and AJAX.
- All shortcodes support pagination, filtering, layout (`grid`/`list`), and custom templates.

#### 5 New Widgets
- **Parent Groups Widget** — All top-level groups with avatars, counts, sorting.
- **Child Groups Widget** — Recursive child rendering, configurable parent ID, max depth.
- **Tag Search Widget** — Search box with autocomplete.
- **Tag Cloud Widget** — Weighted cloud with size scaling.
- **Category Filter Widget** — List or dropdown with counts and active state.

#### Multisite Enhancements
- **Network-wide browsing:** Subsites without BuddyPress can display groups from main site.
- **Subsite widget support:** Network admin toggle for allowing subsite widgets.
- **Default visibility scope** configurable at network level.
- **Group links redirect** to main BuddyPress site from subsites.

### 🔧 Updated Existing Features

#### Actions (Group Create/Edit)
- Parent dropdown respects `BPGH_Permissions` — hides "No Parent" for non-admins when restricted.
- Category selector dropdown on group creation/edit.
- Tag input fields (max 3) on group creation/edit.
- Premium tier info display with upgrade cost.
- Pending approval flag set on child creation by non-admins.

#### Settings Page
- **14 new settings** organized into sections: Display, Features, Permissions, Tooltips, Tag Moderation, Premium/ZCreds, Category Management.
- Yes/No select helper for consistent UI.
- Category management via `slug:Label` textarea.
- Approved tags textarea for moderation.

#### Network Settings
- **Visibility scope controls:** Default visibility for new groups (network/site/hidden).
- **Subsite widget toggle:** Allow/disallow subsite group hierarchy widgets.
- **Network browsing toggle:** Enable/disable cross-site group browsing.

#### Filters
- Category/tag badges in child groups list.
- URL-based meta-query injection for `?bpgh_category`, `?bpgh_tag`, `?bpgh_parent` on groups directory.
- Breadcrumb rendering on single group pages via `bp_before_group_header`.
- Settings-aware: respects `bpgh_show_parent_in_header` and `bpgh_show_children_list` toggles.

#### Youzer Compatibility
- Hierarchy data injected into Youzer group cards (parent link, child count, category, tags).
- Profile header meta shows sub-group count.
- Preserved Youzer widget rendering.
- Template override logic refined for group-specific templates only.

#### Hierarchy Widget
- Works on **activity pages** and **member profile pages**, not just group pages.
- Fallback group ID configuration for non-group page contexts.
- Avatar display option.
- Category/tag meta display option.
- Tooltip data attributes on group links.

#### Multisite Widget
- Auto-detects BuddyPress main site when BP not active on selected site.
- Avatar display support.
- Member count display.
- Category badge display.
- Configurable max depth.
- Respects network admin `bpgh_allow_subsite_widgets` setting.

#### Uninstall
- Cleans up all 12 new meta keys (`bpgh_tags`, `bpgh_category`, `bpgh_premium_tier`, `bpgh_premium_bg_*`, `bpgh_premium_color_*`, `bpgh_premium_typography`, `bpgh_pending_approval`, `bpgh_visibility`).
- Removes all 18 new single-site options.
- Removes all 4 network options.

#### Loader
- Loads 8 new PHP modules after BuddyPress.
- Registers 7 widgets (2 existing + 5 new).
- Enqueues `bpgh-hierarchy.css` on groups, activity, and member pages.
- Expanded `bpgh_should_enqueue_assets()` to include `bp_is_activity_component()` and `bp_is_user()`.

### 📦 New Files
| File | Description |
|------|-------------|
| `bp-group-hierarchy-tags.php` | Tag system CRUD, queries, moderation |
| `bp-group-hierarchy-categories.php` | Category management, assignment, counts |
| `bp-group-hierarchy-permissions.php` | Role-based creation, approval workflow, visibility |
| `bp-group-hierarchy-premium.php` | Premium tiers, ZCreds upgrade, backgrounds |
| `bp-group-hierarchy-tooltips.php` | AJAX tooltip data, rendering, asset enqueue |
| `bp-group-hierarchy-group-types.php` | BP group type integration, filtering |
| `bp-group-hierarchy-ajax.php` | AJAX endpoints: filter, autocomplete, approve, upgrade |
| `bp-group-hierarchy-shortcodes.php` | 6 shortcodes with pagination and filtering |
| `class-bpgh-tag-search-widget.php` | Tag search widget |
| `class-bpgh-tag-cloud-widget.php` | Tag cloud widget |
| `class-bpgh-category-filter-widget.php` | Category filter widget |
| `class-bpgh-parent-groups-widget.php` | Parent groups widget |
| `class-bpgh-child-groups-widget.php` | Child groups widget |
| `assets/css/bpgh-tooltips.css` | Tooltip styles |
| `assets/css/bpgh-hierarchy.css` | Full layout, widgets, directory, responsive |
| `assets/js/bpgh-tooltips.js` | Tooltip hover handler, AJAX, caching |
| `assets/js/bpgh-ajax-filter.js` | Directory filter, pagination, autocomplete |
| `templates/parent-groups.php` | Parent groups page template |

### 🔄 Updated Files
| File | Changes |
|------|---------|
| `bp-group-hierarchy.php` | Version bump to 2.0.0, updated description |
| `bp-group-hierarchy-loader.php` | Rewritten: loads modules, registers widgets, expanded enqueue |
| `bp-group-hierarchy-actions.php` | Category/tag/premium fields, permission checks |
| `bp-group-hierarchy-settings.php` | 14 new settings, 6 sections, category management |
| `bp-group-hierarchy-network-settings.php` | Visibility scopes, subsite widgets, network browsing |
| `bp-group-hierarchy-filters.php` | URL filtering, breadcrumbs, category/tag badges |
| `bp-group-hierarchy-compat-youzer.php` | Group card data, header meta, refined templates |
| `class-bpgh-hierarchy-widget.php` | Activity/member pages, avatars, meta, fallback ID |
| `class-bpgh-multisite-widget.php` | BP auto-detect, avatars, depth, network toggle |
| `uninstall.php` | All v2.0 meta keys and options cleanup |

### ⚙️ Technical Details
- **Backward compatible** with BuddyPress 10+ and PHP 7.4+ / 8.0+.
- **BP < 12 URL fallback chain:** `bp_get_group_url()` → `bp_get_group_permalink()` → `#`.
- **myCred integration** is optional — premium features degrade gracefully without myCred.
- **Youzer compatibility** preserved and extended.
- **All widgets** work on group pages, activity pages, and member profile pages.
- **Modular architecture:** Each feature in a separate PHP file, loaded conditionally.


## [1.1.0] — 2026-04-14

### 🐛 Critical Bug Fixes

- **Fixed fatal error:** Implemented missing `get_ancestors()` method called by Youzer compatibility layer — sites with Youzer active would crash on every group page.
- **Fixed fatal error:** Implemented missing `get_tree()` method called by multisite widget — widget would crash on render.
- **Fixed infinite loop:** Added circular-reference guard and `MAX_DEPTH` constant (50) to `is_descendant_of()`. Corrupted group meta can no longer freeze PHP.
- **Removed duplicate class:** Deleted `bp-group-hierarchy-widget.php` which redeclared `BPGH_Hierarchy_Widget` (already defined in `class-bpgh-hierarchy-widget.php`).
- **Removed legacy dead code:** Deleted `install.php` — referenced wrong class names, used `create_function()` (removed in PHP 8.0), and deprecated BuddyPress globals.

### 🔒 Security Fixes

- **CSRF on parent group save:** Added `wp_nonce_field()` / `wp_verify_nonce()` to `bpgh_save_parent_group()`. Previously accepted `$_POST` data with zero nonce verification.
- **CSRF on network settings:** Added nonce verification to `bpgh_save_network_settings()`. Previously checked capability but never verified the nonce.
- **Youzer template filter:** Changed `bpgh_youzer_disable_bp_templates()` to return empty string instead of `false`, which broke subsequent `bp_locate_template` filters.

### ⚡ Performance Improvements

- **`get_children()` meta query:** Replaced full-table scan (`per_page => false` + PHP filter) with BuddyPress `meta_query` targeting `bpgh_parent_id` directly.
- **Per-request parent cache:** Added static `$parent_cache` to `BP_Group_Hierarchy` to avoid redundant `groups_get_groupmeta()` calls within a single request.
- **Consolidated Youzer checks:** Extracted duplicate Youzer detection logic from CSS/JS enqueue functions into shared `bpgh_should_enqueue_assets()` helper.
- **Capped dropdown query:** Parent group dropdown now queries max 500 groups instead of unlimited.

### 🔧 Compatibility

- **BP < 12 URL fallback:** Widget group links use `bp_get_group_url()` → `bp_get_group_permalink()` → `#` fallback chain.
- **Modern `groups_get_group()` syntax:** Passes integer ID directly instead of deprecated `array('group_id' => $id)`.
- **Replaced `get_blog_details()`:** Multisite widget now uses `get_site()` (deprecated since WP 4.7).
- **Added plugin headers:** Text Domain, Requires PHP (7.4), Requires at least (5.6), License URI.

### ✨ New Features

- **`BPGH_VERSION` constant** — single source of truth for cache-busting and compatibility checks.
- **Admin notice** — displays a clear error when BuddyPress is not active (instead of silent failure).
- **New API methods:** `get_ancestors()`, `get_tree()`, `get_siblings()`, `get_depth()`, `flush_cache()`.
- **Enhanced `uninstall.php`** — cleans up single-site options, network options, and `bpgh_parent_id` group meta.
