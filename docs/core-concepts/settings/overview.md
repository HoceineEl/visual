# Settings

## What Are Settings?

Settings are the backbone of how customization works in Bagisto Visual.
They allow merchants to configure sections, blocks, and even the entire storefront layout visually through the Theme Editor — without touching code.

Settings make it easy to personalize storefronts, ensuring flexibility for developers and simplicity for merchants.

Bagisto Visual settings can be used in two main places:

| Setting Type           | Scope                                  | Example                                         |
| ---------------------- | -------------------------------------- | ----------------------------------------------- |
| Section/Block Settings | Control individual sections and blocks | Banner text, Button color, Product grid columns |
| Theme Settings         | Global store-wide options              | Typography, Color palette, default styles       |

## How Settings Work

- **Section and Block Settings** are defined inside each section class using a structured PHP API.
- **Theme Settings** are defined globally and control the broader layout and style of the storefront.

Settings provide an abstraction that allows customization without changing the underlying code. Developers expose options, and merchants adjust them through an intuitive visual interface.

## Setting Input Types

Bagisto Visual supports a wide variety of input types for settings, including:

| Type     | Purpose              | Example                             |
| :------- | :------------------- | :---------------------------------- |
| Text     | Simple text input    | Section headings, Button labels     |
| Textarea | Multiline text input | Detailed descriptions               |
| Link     | URL input            | Link buttons, Banners               |
| Color    | Color picker         | Background colors, Text colors      |
| Select   | Dropdown option list | Layout styles, Alignment choices    |
| Range    | Numeric sliders      | Number of columns, Spacing settings |
| Switch   | On/Off toggle        | Show/Hide features                  |
| Category | Category selector    | Featured categories, Menus          |

Each input type improves merchant experience by offering the most natural way to configure the setting.

## Editing Settings in the Theme Editor

- Merchants can interact with settings directly through the **Theme Editor**.
- They can **instantly preview changes** without needing to publish.
- **Section and Block Settings** affect only their associated components.
- **Theme Settings** affect the layout and design globally across the storefront.

Settings changes are safe, reversible, and visual, making customization intuitive even for non-technical users.

---

Settings are a key reason why Bagisto Visual empowers both developers and merchants to create rich, personalized storefronts without friction.
