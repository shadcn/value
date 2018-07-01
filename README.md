# Value
---
Simplified Twig template variables for Drupal.

Examples:

1. Text fields: `{{ _article.title }}`
2. Text with summary: `{{ _artile.body.value }}` or `{{ artile.body.summary }}` for the summary.
3. Link: `{{ _article.field_link.url }}`
4. Image: `<img src="{{ _article.field_image.url }} alt="{{ _article.field_image.alt }}" />`
5. Reference: `{{ _article.field_author.field_name }}`
6. And more...

### Filters
The value module also ships with some helpful Twig filters.

1. Image style: `{{ _article.field_image.uri|image_style('thumbnail') }}`
2. Markup: `{{ _article.body|markup }}` for rendering safe HTML markup.
3. Truncate: `{{ _article.title|truncate(10) }}` will truncate title to 10 characters.
4. Words: `{{ _article.title|words(5) }}` will truncate title to 5 words.
5. Pick: `{{ _article|pick(['field_name', 'field_author']) }}` to pick some values only.
6. Rename keys: `{{ _article|rename_keys(field_title: 'title') }}` to rename keys.

### Need help?

Create an issue in the [issue queue](https://www.drupal.org/project/issues/value).
