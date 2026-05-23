# PR: v2.0.0 — Full Group Management Ecosystem

## Summary

Major release transforming BP Group Hierarchy from a simple parent–child plugin into a complete group management ecosystem. Adds 24 features including group tags, categories, role-based permissions, premium tiers with ZCreds/myCred, AJAX tooltips, 6 shortcodes, 5 new widgets, enhanced group directory with AJAX filtering, and full multisite support — all built as modular PHP files loaded conditionally.

**Built on v1.1.0** which fixed 5 critical bugs, 3 security vulnerabilities, and performance regressions.

## Architecture

All new features are built as **separate PHP module files** loaded conditionally after BuddyPress:

```
bp-group-hierarchy/
├── bp-group-hierarchy.php              (bootstrap — v2.0.0)
├── bp-group-hierarchy-loader.php       (rewritten — loads all modules)
├── bp-group-hierarchy-classes.php      (core hierarchy class — unchanged)
├── bp-group-hierarchy-actions.php      (updated — category/tag/permission fields)
├── bp-group-hierarchy-filters.php      (updated — URL filtering, breadcrumbs)
├── bp-group-hierarchy-settings.php     (updated — 14 new settings)
├── bp-group-hierarchy-network-settings.php (updated — visibility scopes)
├── bp-group-hierarchy-compat-youzer.php (updated — group card data)
├── bp-group-hierarchy-tags.php         ★ NEW
├── bp-group-hierarchy-categories.php   ★ NEW
├── bp-group-hierarchy-permissions.php  ★ NEW
├── bp-group-hierarchy-premium.php      ★ NEW
├── bp-group-hierarchy-tooltips.php     ★ NEW
├── bp-group-hierarchy-group-types.php  ★ NEW
├── bp-group-hierarchy-ajax.php         ★ NEW
├── bp-group-hierarchy-shortcodes.php   ★ NEW
├── class-bpgh-hierarchy-widget.php     (updated — activity/member pages)
├── class-bpgh-multisite-widget.php     (updated — BP auto-detect)
├── class-bpgh-tag-search-widget.php    ★ NEW
├── class-bpgh-tag-cloud-widget.php     ★ NEW
├── class-bpgh-category-filter-widget.php ★ NEW
├── class-bpgh-parent-groups-widget.php ★ NEW
├── class-bpgh-child-groups-widget.php  ★ NEW
├── uninstall.php                       (updated — all v2 cleanup)
├── templates/
│   └── parent-groups.php               ★ NEW
├── assets/
│   ├── css/
│   │   ├── bpgh-tooltips.css           ★ NEW
│   │   └── bpgh-hierarchy.css          ★ NEW
│   └── js/
│       ├── bpgh-tooltips.js            ★ NEW
│       └── bpgh-ajax-filter.js         ★ NEW
├── CHANGELOG.md                        (updated)
└── PR_DESCRIPTION.md                   (updated)
```

## Features Added

### 1. Group Tags System
- Up to 3 tags per group, stored as serialized array in group meta
- Tag search widget with autocomplete
- Tag cloud widget with weighted sizing
- Optional tag moderation (admin-approved tags only)
- URL-based tag filtering on group directory

### 2. Group Categories
- Admin-defined categories (slug:Label format)
- Category assignment per group
- Category filter widget (list or dropdown mode)
- URL-based category filtering on group directory

### 3. Role-Based Permissions
- Parent group creation restricted to admins (configurable)
- Child group approval workflow for non-admins
- Visibility inheritance (child inherits parent scope)
- Three visibility scopes: network, site, hidden

### 4. Premium Tiers & ZCreds
- Free/Premium group tiers
- myCred-powered ZCreds upgrade system
- Premium features: custom backgrounds, color schemes, typography
- Premium badge on cards and tooltips
- Configurable upgrade cost

### 5. Hover Tooltips
- AJAX-powered tooltips showing avatar, description, admins, member count, tags, category, premium badge
- Smart viewport positioning
- Response caching
- Configurable content display

### 6. BuddyPress Group Types Integration
- Hierarchy-aware type filtering
- Type filter tabs on directory
- Admin columns in BP group admin
- URL-based type filtering

### 7. Enhanced Group Directory
- AJAX filtering: category + tag + type + sort + parent
- Group cards with full metadata
- Breadcrumbs (Parent → Child → Group)
- "View Child Groups" / "Back to Parent" links
- Sorting by name, date, activity, member count

### 8. Six Shortcodes
- `[bpgh_parent_groups]` — Top-level groups with avatars, tooltips, sorting
- `[bpgh_child_groups parent="123"]` — Child groups for specific parent
- `[bpgh_group_categories]` — Category listing with counts
- `[bpgh_group_tags]` — Tag cloud or list
- `[bpgh_network_groups]` — Network-wide groups (multisite)
- `[bpgh_group_directory]` — Enhanced directory with AJAX filters

### 9. Five New Widgets
- Parent Groups Widget
- Child Groups Widget
- Tag Search Widget
- Tag Cloud Widget
- Category Filter Widget

### 10. Multisite Enhancements
- Subsites without BuddyPress can browse groups from main site
- Network admin toggle for subsite widgets
- Default visibility scope at network level
- Auto-detection of BuddyPress main site

### 11–24. UI Enhancements
- Widgets on activity + member pages (not just group pages)
- Youzer group card integration
- Avatar display in all widgets
- Dedicated parent groups page template
- Responsive CSS with grid/list layouts

## Updated Existing Files

| File | Changes |
|------|---------|
| `bp-group-hierarchy.php` | Version 2.0.0, updated description |
| `bp-group-hierarchy-loader.php` | Rewritten: 8 module loads, 7 widget registrations, expanded enqueue |
| `bp-group-hierarchy-actions.php` | Category/tag/premium fields, permission-aware dropdown |
| `bp-group-hierarchy-settings.php` | 14 new settings in 6 sections |
| `bp-group-hierarchy-network-settings.php` | Visibility scopes, subsite widgets, network browsing |
| `bp-group-hierarchy-filters.php` | URL meta-query injection, breadcrumbs, category/tag badges |
| `bp-group-hierarchy-compat-youzer.php` | Group card data, header meta, refined template override |
| `class-bpgh-hierarchy-widget.php` | Activity/member page support, avatars, meta, fallback ID |
| `class-bpgh-multisite-widget.php` | BP auto-detect, avatars, depth config, network toggle |
| `uninstall.php` | 12 meta keys + 18 options + 4 network options cleanup |

## Data Model

### Group Meta Keys (new)
| Key | Type | Description |
|-----|------|-------------|
| `bpgh_tags` | serialized array | Up to 3 tags per group |
| `bpgh_category` | string (slug) | Assigned category |
| `bpgh_premium_tier` | string | 'free' or 'premium' |
| `bpgh_premium_bg_image` | string (URL) | Premium background image |
| `bpgh_premium_bg_animated` | string (URL) | Premium animated background |
| `bpgh_premium_bg_video` | string (URL) | Premium video background |
| `bpgh_premium_color_primary` | string (hex) | Premium primary color |
| `bpgh_premium_color_secondary` | string (hex) | Premium secondary color |
| `bpgh_premium_color_accent` | string (hex) | Premium accent color |
| `bpgh_premium_typography` | string | Premium typography setting |
| `bpgh_pending_approval` | bool | Whether group is pending admin approval |
| `bpgh_visibility` | string | 'network', 'site', or 'hidden' |

### WP Options (new)
| Option | Default | Description |
|--------|---------|-------------|
| `bpgh_enable_tags` | yes | Enable tag system |
| `bpgh_enable_categories` | yes | Enable categories |
| `bpgh_enable_premium` | no | Enable premium tiers |
| `bpgh_enable_tooltips` | yes | Enable hover tooltips |
| `bpgh_parent_creation_role` | admin | Who can create parent groups |
| `bpgh_require_child_approval` | yes | Require approval for child groups |
| `bpgh_premium_cost` | 100 | ZCreds cost for premium upgrade |
| `bpgh_zcred_point_type` | mycred_default | myCred point type slug |
| `bpgh_tag_moderation` | no | Require tag approval |
| `bpgh_approved_tags` | (empty) | Comma-separated approved tags |
| `bpgh_tooltip_show_admins` | yes | Show admins in tooltips |
| `bpgh_tooltip_show_tags` | yes | Show tags in tooltips |
| `bpgh_tooltip_show_category` | yes | Show category in tooltips |
| `bpgh_visibility_inheritance` | yes | Child inherits parent visibility |
| `bpgh_categories` | array | Admin-defined categories |

### Network Options (new)
| Option | Default | Description |
|--------|---------|-------------|
| `bpgh_default_visibility` | network | Default visibility for new groups |
| `bpgh_allow_subsite_widgets` | yes | Allow subsite hierarchy widgets |
| `bpgh_network_browsing` | yes | Allow cross-site group browsing |

## Compatibility

- **PHP:** 7.4+ / 8.0+ / 8.1+ / 8.2+
- **WordPress:** 5.6+
- **BuddyPress:** 10+ (with BP 12+ URL API support)
- **Youzer:** Full compatibility maintained and extended
- **myCred:** Optional dependency for ZCreds/premium features
- **Multisite:** Full support with network admin settings

## Testing Checklist

### Tags
- [ ] Create a group with 3 tags — verify tags are saved and displayed
- [ ] Try adding a 4th tag — verify it's ignored
- [ ] Enable tag moderation — verify only approved tags can be used
- [ ] Use tag search widget — verify autocomplete works
- [ ] Click a tag in tag cloud — verify directory filters correctly

### Categories
- [ ] Add categories in admin settings (slug:Label format)
- [ ] Assign category to a group — verify badge displays
- [ ] Use category filter widget — verify filtering works
- [ ] Filter via URL `?bpgh_category=slug` — verify meta query

### Permissions
- [ ] Set parent creation to "admin only" — verify non-admins can't create top-level groups
- [ ] Create child group as non-admin — verify pending approval status
- [ ] Approve/reject pending group via admin — verify AJAX action works
- [ ] Check visibility inheritance — verify child inherits parent scope

### Premium
- [ ] Enable premium tiers — verify upgrade button appears
- [ ] Upgrade group with ZCreds (requires myCred) — verify tier changes
- [ ] Premium badge displays on cards and tooltips
- [ ] Premium features (background, colors) apply to group page

### Tooltips
- [ ] Hover over group name — verify tooltip appears with AJAX data
- [ ] Verify tooltip shows avatar, description, admins, member count
- [ ] Verify tooltip position adjusts near viewport edges
- [ ] Toggle tooltip content settings — verify changes reflected

### Shortcodes
- [ ] `[bpgh_parent_groups]` — renders top-level groups with pagination
- [ ] `[bpgh_child_groups parent="X"]` — renders child groups for parent X
- [ ] `[bpgh_group_categories]` — renders category list with counts
- [ ] `[bpgh_group_tags]` — renders tag cloud
- [ ] `[bpgh_network_groups]` — renders network groups (multisite only)
- [ ] `[bpgh_group_directory]` — renders enhanced directory with AJAX

### Widgets
- [ ] All 7 widgets appear in widget admin
- [ ] Widgets render on group pages
- [ ] Widgets render on activity pages
- [ ] Widgets render on member profile pages
- [ ] Tag search autocomplete works
- [ ] Category filter updates directory

### Multisite
- [ ] Subsite without BP displays groups from main site
- [ ] Network admin visibility settings save correctly
- [ ] "Allow subsite widgets" toggle works
- [ ] Group links redirect to main BP site

### Youzer
- [ ] With Youzer active, breadcrumbs render in group header
- [ ] Group cards show hierarchy data (parent, children, category, tags)
- [ ] Youzer widgets are preserved (not overridden)
- [ ] No fatal errors on any page

### Directory
- [ ] AJAX filtering works for all combinations (category + tag + type + sort)
- [ ] Breadcrumbs show on child group pages
- [ ] "View Child Groups" link works
- [ ] Pagination works with AJAX
- [ ] Sort controls change group order

### Backward Compatibility
- [ ] BP 10.x: `bp_get_group_permalink()` fallback used
- [ ] BP 12+: `bp_get_group_url()` used
- [ ] PHP 7.4: No syntax errors
- [ ] PHP 8.0+: No deprecation warnings
- [ ] Sites without myCred: Premium features degrade gracefully
- [ ] Sites without Youzer: All features work normally
