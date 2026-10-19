# Mailster Gravity Forms
Contributors: everpress, mailster, xaverb  
Tags: gravity forms, email marketing, newsletter signup, mailing list, double opt-in
Requires at least: 6.0  
Tested up to: 6.5  
Stable tag: 2.0.0  
License: GPLv2 or later

Connect Gravity Forms to Mailster with field mapping, mailing list assignment, conditional signup, and double opt-in.

## Description

Mailster Gravity Forms connects Gravity Forms with the Mailster email newsletter plugin. Turn any Gravity Forms form into a newsletter signup form and add submitters to your Mailster mailing lists automatically.

Configure the integration separately for each form. Map submitted values to Mailster subscriber fields, choose the mailing lists new subscribers should join, and require an explicit checkbox or radio choice before a subscription is created.

### Gravity Forms to Mailster Integration

- Enable or disable Mailster subscription handling for each Gravity Form.
- Map form inputs to email, first name, last name, and Mailster custom fields.
- Add subscribers to one or more selected Mailster lists.
- Use submitted field values to assign or create Mailster lists dynamically.
- Subscribe only when a chosen checkbox or radio option is selected.
- Enable Mailster double opt-in so subscribers must confirm their email address.
- Update existing Mailster subscribers when they submit the form again.

The add-on requires [Mailster Newsletter Plugin for WordPress](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Gravity+Forms) 4.0 or later and the [Gravity Forms plugin](https://www.gravityforms.com/). Both plugins must be installed and active.

### Common Uses

- Add a newsletter consent checkbox to a contact, registration, or lead form.
- Send subscribers to different mailing lists based on their form choices.
- Collect Mailster custom field data through an existing Gravity Forms form.
- Build confirmed email lists with a double opt-in signup process.

## Installation

1. Install and activate Mailster 4.0 or later and Gravity Forms.
2. Install Mailster Gravity Forms from **Plugins → Add New**, or upload the plugin ZIP.
3. Activate Mailster Gravity Forms.
4. Open **Forms**, edit the form you want to connect, then open **Settings → Mailster**.
5. Enable Mailster for the form and map at least one Gravity Forms email input to the Mailster email field.
6. Select the Mailster lists for new subscribers.
7. Optionally enable double opt-in or a conditional checkbox or radio choice.
8. Save the form settings and test a submission.

## Frequently Asked Questions

### What does the plugin require to work correctly?

You need the [Gravity Forms plugin](https://www.gravityforms.com/) and [Mailster Newsletter Plugin for WordPress](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=Gravity+Forms) 4.0 or later. Install and activate both before configuring the integration.

### Where are the Mailster settings for a Gravity Form?

Edit the form in Gravity Forms, then open **Settings → Mailster**. The settings apply only to that form.

### Which Gravity Forms fields can I send to Mailster?

You can map form inputs to the subscriber email address, first name, last name, and custom fields configured in Mailster. An email mapping is required to create or update a subscriber.

### Can subscribers choose a Mailster mailing list?

Yes. You can select fixed lists in the form settings or map a form field to a Mailster list. A mapped list field uses the submitted value as the list name and creates the list when it does not already exist.

### Can I subscribe someone only when they give consent?

Yes. Enable the conditional check and select a Gravity Forms checkbox or radio option. The subscriber is added only when the configured choice is submitted.

### Does the integration support double opt-in?

Yes. Enable **Double Opt In** in the form's Mailster settings. New subscribers remain pending until they confirm through Mailster's confirmation email.

### What happens when an existing Mailster subscriber submits the form?

The integration updates that subscriber's mapped data and list assignments instead of creating a duplicate. A pending subscriber can receive another confirmation email.

## Screenshots

### 1. Form Settings

![Form Settings](https://ps.w.org/mailster-gravity-forms/assets/screenshot-1.png)

## Changelog

### Unreleased

- fixed: resubmitting a form for one pending subscriber no longer sends a confirmation email to every pending subscriber
- fixed: conditional signup still matches radio choices whose values contain percent-encoded characters
- fixed: form settings escape field labels and sanitize saved Mailster options
- fixed: conditional field options no longer start with the stray text "sss"

### 2.0.0

- update for Gravity forms 2.5

### 1.1.2

- fixed: resend confirmations on pending subscribers if form as been submitted.

### 1.1.1

- fixed: prevent sending duplicate "new subscribers" notifications

### 1.1

- you can now let users decide which lists they sign up to.
- smaller fixes and improvements.

### 1.0.4

- support for Mailster 2.3

### 1.0.3

- fixed: status not changed if subscriber already exists

### 1.0.2

- removed legacy form option which causes PHP Notice

### 1.0.1

- fixed: PHP warnings

### 1.0

- initial release
