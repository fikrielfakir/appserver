# Design Guidelines: Android Platform Control & Customization System

## Design Approach
**Selected Framework:** Material Design 3 (Material You)
**Justification:** This enterprise dashboard requires a robust, data-focused design system that excels at information density, form handling, and data visualization. Material Design 3 provides the necessary components for complex admin interfaces while maintaining clarity and usability.

## Core Design Elements

### A. Typography System
**Font Family:** Roboto (via Google Fonts CDN)
- **Display/Headers:** Roboto, 500 weight
  - H1: 2.5rem (40px) - Page titles
  - H2: 2rem (32px) - Section headers
  - H3: 1.5rem (24px) - Card/panel headers
  - H4: 1.25rem (20px) - Subsection headers

- **Body Text:** Roboto, 400 weight
  - Large: 1rem (16px) - Primary content
  - Medium: 0.875rem (14px) - Secondary content, table cells
  - Small: 0.75rem (12px) - Labels, captions, metadata

- **UI Elements:** Roboto, 500 weight
  - Buttons: 0.875rem (14px), uppercase with letter-spacing: 0.5px
  - Navigation: 0.875rem (14px)
  - Form labels: 0.875rem (14px), 500 weight

### B. Layout System
**Spacing Units:** Use Tailwind spacing: 1, 2, 3, 4, 6, 8, 12, 16, 20, 24
- Micro spacing (within components): 1, 2, 3
- Component spacing: 4, 6, 8
- Section spacing: 12, 16, 20, 24

**Grid Structure:**
- Dashboard container: `max-w-[1440px]` with `px-6`
- Content columns: 12-column grid system
- Sidebar: Fixed 256px width (w-64)
- Main content: Fluid width with padding
- Card grids: 2-4 columns responsive (grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4)

**Vertical Rhythm:**
- Page padding: `py-6` to `py-8`
- Section separation: `mb-8` to `mb-12`
- Card internal padding: `p-6`
- Form field spacing: `space-y-4`

### C. Component Library

#### Navigation & Structure
**Top App Bar:**
- Height: 64px (h-16)
- Contains: Logo (left), search (center), user profile/notifications (right)
- Elevated with subtle shadow
- Sticky positioning

**Side Navigation:**
- Width: 256px (w-64), collapsible to icon-only 72px (w-18)
- Sections: Dashboard, Apps, AdMob Accounts, Notifications, Analytics, Settings
- Active state: Highlighted background, icon emphasis
- Icon library: Material Icons or Heroicons (choose one)
- Item height: 48px with 12px vertical padding

**Breadcrumbs:**
- Position: Below app bar, above page title
- Size: 0.875rem with chevron separators
- Interactive links with hover states

#### Data Display Components
**Cards:**
- Rounded corners: `rounded-lg` (8px)
- Elevation: subtle shadow for hierarchy
- Padding: `p-6`
- Header with action buttons (right-aligned)
- Dividers between sections

**Tables:**
- Striped rows for readability (odd/even pattern)
- Header: 500 weight, 0.75rem uppercase with letter-spacing
- Row height: 56px minimum
- Cell padding: `px-4 py-3`
- Sortable columns with icons
- Pagination: Bottom-aligned, shows "1-10 of 100 items"
- Action columns (right-aligned): icon buttons for edit/delete
- Responsive: Stack to cards on mobile

**Stats/Metrics Cards:**
- Compact card layout
- Large number: 2rem, 500 weight
- Label below: 0.875rem
- Icon: 24px, positioned top-right
- Trend indicator: Small arrow icon with percentage
- Grid: 4 columns desktop, 2 tablet, 1 mobile

**Charts & Graphs:**
- Use Chart.js or Recharts library
- Consistent axis labels: 0.75rem
- Legend position: top-right or bottom
- Tooltips on hover with detailed data
- Height: 300px-400px for primary charts

#### Forms & Input Components
**Form Layout:**
- Two-column grid for related fields: `grid grid-cols-1 lg:grid-cols-2 gap-6`
- Full-width for long fields (textareas, rich editors)
- Fieldset grouping with subtle borders
- Form section headers: H4 with `mb-4`

**Input Fields:**
- Height: 48px (h-12)
- Padding: `px-4 py-3`
- Border: 1px with rounded corners `rounded-md`
- Label: Above input, `mb-2`, 0.875rem, 500 weight
- Helper text: Below input, 0.75rem
- Error state: Red border with error message below
- Focus state: Emphasized border

**Buttons:**
- Primary: Height 40px (h-10), padding `px-6 py-2`, rounded `rounded-md`
- Secondary: Outlined variant with same dimensions
- Text buttons: No background, padding `px-4 py-2`
- Icon buttons: 40px square (w-10 h-10), centered icon
- Button groups: Adjacent buttons with shared borders

**Select Dropdowns:**
- Same height as inputs (48px)
- Chevron icon (right-aligned)
- Dropdown menu: Elevated card with max-height and scroll
- Option height: 40px with `px-4 py-2`

**Switches & Checkboxes:**
- Switch: Material Design 3 style, 20px height
- Checkbox: 20px square with checkmark
- Radio: 20px circle
- Label: Right-aligned, 0.875rem

**Rich Text Editor (Notifications):**
- Toolbar: Top-positioned, 48px height
- Editor area: Min-height 200px, bordered
- Preview mode: Side-by-side or tabbed

#### Action Components
**Modals/Dialogs:**
- Max width: 600px for forms, 900px for complex content
- Rounded: `rounded-lg`
- Header: `px-6 py-4` with close button
- Content: `px-6 py-4`
- Footer: `px-6 py-4`, buttons right-aligned
- Overlay: Semi-transparent backdrop

**Notifications/Alerts:**
- Toast position: Top-right, stacked
- Width: 360px
- Auto-dismiss: 5 seconds
- Types: Success, error, warning, info
- Icon: Left-aligned, 20px

**Tabs:**
- Horizontal tabs for navigation within pages
- Tab height: 48px
- Active indicator: Bottom border, emphasized text
- Icon + text combination

#### Dashboard-Specific Components
**AdMob Account Cards:**
- Display account name, status badge, priority
- Key metrics: Impressions, revenue, fill rate (horizontal layout)
- Action menu: Kebab menu (top-right)
- Edit/disable quick actions
- Grid: 3 columns desktop, 2 tablet, 1 mobile

**Notification Builder:**
- Left panel: Form fields (type, title, message, targeting)
- Right panel: Live preview of notification
- Media upload: Drag-and-drop zone
- Action button configuration: Expandable section
- Scheduling: Date/time picker with timezone selector

**Analytics Dashboard:**
- Top section: Key metrics (4-column grid)
- Chart section: Line graph for trends (full-width)
- Breakdown section: Pie/donut charts (2-column)
- Data table: Detailed metrics at bottom

**App Switcher:**
- Dropdown in top bar
- Lists all managed apps with package names
- Search/filter capability
- Selected app highlighted

### D. Responsive Behavior
**Breakpoints:**
- Mobile: < 640px - Single column, hamburger menu
- Tablet: 640px-1024px - Two columns, visible sidebar
- Desktop: > 1024px - Full multi-column layouts

**Mobile Adaptations:**
- Side navigation: Drawer overlay
- Tables: Convert to stacked cards
- Multi-column forms: Single column
- Stats: 1-2 columns maximum
- Hidden elements: Collapsible sections

### E. Iconography
**Icon Library:** Heroicons (via CDN)
- Size standard: 20px for inline, 24px for standalone
- Use: Outline style for navigation, solid for actions
- Consistency: Same icon for same action throughout

**Key Icons Needed:**
- Dashboard: chart-bar
- Apps: device-phone-mobile
- AdMob: currency-dollar
- Notifications: bell
- Analytics: chart-line
- Settings: cog
- Edit: pencil
- Delete: trash
- Add: plus
- Search: magnifying-glass
- User: user-circle

### F. Content Guidelines
**Empty States:**
- Centered content with icon (48px)
- Explanatory text: "No apps added yet"
- Primary action button: "Add Your First App"

**Loading States:**
- Skeleton screens for data tables
- Spinner for page loads (centered, 40px)
- Progressive loading for charts

**Data Visualization:**
- Color-coded status indicators (success/warning/error patterns)
- Percentage bars for metrics
- Trend arrows (up/down) for comparisons

This design system provides a comprehensive, professional foundation for building a scalable admin dashboard that prioritizes functionality, data clarity, and efficient workflow management for technical users.