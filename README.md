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

## How to install on a marketplace
1. Download the repository as `.zip` file.
2. Extract the file "JWT-Auth-SSO-master" inside the root `.zip` file.
3. Compress all its contents into another `.zip` file, such that the resulting root folder contains the contents of this repository
4. This `.zip` file is what you install on your [developer dashboard](https://dashboard.sandbox.arcadier.io).
5. Go to your marketplace and install the Plug-In you created.

## How to use

1. Change the public and private keys. The Public and Private keys are found in the [key]() folder.
    * `certificate.crt` contains the public key.
    * `secret_pem_file.pem` contains the private key.

2. For testing purposes, the file [encryption.php]() was created, to simulate encryption. This PHP file simply creates a JWT token for a specified user.

    Creating a user's JWT token:

    1. Hardcode the details of the user in [encryption.php]() in the following private claims:

    ```php
    ->claim('first_name', 'John')
    ->claim('last_name', 'Smith')
    ->claim('email', 'johnsmith@gmail.com')
    ```

    2. Save and follow instructions of [How to install]()

    3. Simulate the external platform generating the JWT token via this link: `https://{marketplace-name}.arcadier.io/user/plugins/{plug-in-ID}/encryption.php?userCode=`

        where `userCode` is the user's ID in the external platform. (For testing purposes, this can be anything)

    4. The output will be the JWT token of "John Smith" that the external platform will pass to Arcadier.

3. Simulate Arcadier receiving and decrypting the JWT token [decryption.php]() by calling this link: `https://{marketplace-name}.arcadier.io/user/plugins/{plug-in-ID}/decryption.php?returnUrl={__}&ssoToken={__}`


    where `returnUrl` is the URL slug of the Arcadier page you want the user to be redirected to. Example:

    * Item Detail Page: `returnUrl=/User/Item/Detail/Shoe/66056`
    * User Settings Page: `returnUrl=/user/marketplace/user-settings`
    
Successfully completing all the steps above will log John Smith on to Arcadier and redirect him to the specified `returnUrl`.
