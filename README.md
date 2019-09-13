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

