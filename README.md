# JWT Auth SSO

## Contents
* [Brief Introduction]()
* [Installing JWT via composer]()


### Brief Introduction
Plug-Ins have the same folder structure as the directory shown in this repository. (`key` and `sdk` are optional and only relevant here)

Anything found in the `admin` folder is meant for the marketplace’s admin portal, and only accessible by an admin authenticated session.

Anything found in the `user` folder is meant for the users (merchants, buyers, and non-authenticated users). Limiting access to those files is optional, and described in our [Coding Tutorials](https://github.com/Arcadier/Coding-Tutorials/blob/master/Selecting%20on%20which%20page%20and%20for%20which%20user%20my%20code%20executes.md).

The decryption can be performed by a PHP file residing in the `user` folder, which is accessible via a URL. If the decryption file is named ‘decryption.php’, the URL will look like this:

> {the-marketplace}.arcadier.io/users/plugins/{package_ID_of_plugin}/decryption.php

Note: this can be easily customised/shortened using our [Custom URL API](https://apiv2.arcadier.com/?version=latest#4b934939-cd65-4ed0-aee9-3b15de47904b).

### Installing JWT via composer
1. Clone this repository
The required dependencies and versions for JWT are pre defined in `composer.json` and `composer.lock`

2. Run the following command in your directory containing the cloned repository.

```
php composer.phar install
```

3. Done.
