# S3 Backup

The `s3backup` cli tool uses a configuration file to setup and backup directories on your computer to S3.

## Install
`composer global require "marcusmyers/s3backup=~1.0.0"`

Make sure to place the ~/.composer/vendor/bin directory in your PATH so the s3 executable is found when you run the `s3 backup` command in your terminal.

## Configure

Once installed on your machine it should create the following directory `$HOME/.s3backup` and copy the `config.json.example` to that directory.  Rename the `$HOME/.s3backup/config.json.example` file to `$HOME/.s3backup/config.json`.  Open up the `config.json` file and edit it to your liking, below is an example:

```json
{
  "aws": {
    "credentials": {
      "key": "<Your AWS Access Key ID>",
      "secret": "<Your AWS Secret Access Key ID>"
    },
    "bucket": "my-bucket",
    "file_prefix": ""
  },
  "filename": "my_laptop_backup",
  "directories": [
    "/Users/exampleUser/Desktop/Logos",
    "/var/www/html",
    "/Users/exampleUser/Documents"
  ]
}
```
