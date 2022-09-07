# Miniblog Blog Project

This repo is used in conjunction with [Composer](https://getcomposer.org/) to create an instance of [Miniblog](https://github.com/miniblog/engine) that can be version-controlled and customised.

## Instructions

1. Assuming you installed Composer globally, run `composer create-project miniblog/blog-project <target-directory>`.  Replace `<target-directory>` with the name of the directory you want to create.
1. Update the few values in `config.php`.
1. Make `public/` the document root of your website.

You should now see the Miniblog homepage when you navigate to the root of your website.

You can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.
