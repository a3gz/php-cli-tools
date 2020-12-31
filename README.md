# PHP CLI tools

A set of tools to generate distributable versions of assets. 

## Tools

### Copy
Copy a list of files as per a JSON specification file. 

    {
      "/path/to/src/file": "path/to/dist/file"
    }

Use this tool to copy files without modifying them.

## Minify
Minifies the input file and saves the result in the destination directory.

Use this tool to minify HTML, Javascript and CSS files.

If you write SASS instead of vanilla CSS, then you may want to use the next tool in the box instead.

## SASS to CSS
Convert all `.scss` files found in the source directory and writes the corresponding `.css` files in the target.
This tool uses `leafo/scssphp` to make the conversion.

## Revision
It may be usefull to have a revision number generated each time we build assets so we can decide whether to push changes to a server or not. 

    composer run-script cli-revision path/to/directory [file-name]

This tool will create a file under `path/to/directory` with a timestamp as content. If `file-name` is given, the file will have that name otherwise it will fallback to `a3gz-php-cli-tools.revision`.

If the CI pipeline uses PHPloy, we can test this file for changes to deploy the generated assets:
The line below will instruct PHPloy to deploy everything under `dist/` if the revision file changed since last revision.

    include[] = 'dist/:path/to/directory/a3gz-php-cli-tools.revision'

For this to work we need to add the revision file to version control.
