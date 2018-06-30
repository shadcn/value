# Value
The value module simplifies the `$variables` array for Twig templates. The goal is to make field values
easily accessible and make extending templates easier.

Examples:

1. Text fields: `{{ _article.title }}`
2. Text with summary: `{{ _artile.body.value }}` or `{{ artile.body.summary }}` for the summary.
3. Link: `{{ _article.field_link.url }}`
4. Image: `<img src="{{ _article.field_image.url }} alt="{{ _article.field_image.alt }}" />`
5. Reference: `{{ _article.field_tags[0].name }}`

### Filters
The value module also ships with some helpful Twig filters.

1. Image style: `{{ _article.field_image.uri|image_style('thumbnail') }}`
2. Markup: `{{ _article.body.value|markup }}` for rendering safe HTML markup.
3. Truncate: `{{ _article.title|truncate(10) }}` will truncate title to 10 characters.
4. Words: `{{ _article.title|words(5) }}` will truncate title to 5 words.

### Need help?

Create an issue in the [issue queue](https://www.drupal.org/project/issues/value).
