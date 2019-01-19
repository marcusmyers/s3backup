# S3 Backup

The `s3backup` cli tool uses a configuration file to setup and backup directories on your computer to S3.

## Install
`composer global require "marcusmyers/s3backup=~2.0.0"`

Make sure to place the ~/.composer/vendor/bin directory in your PATH so the s3 executable is found when you run the `s3backup backup` command in your terminal.

## Configure

Once installed on your machine you will need to run `s3backup init` as it should create the following directory `$HOME/.s3backup` and `config.json` file in that directory. Open up the `config.json` file and edit it to your liking, below are a couple examples:

```json
{
  "aws": {
    "credentials": {
      "key": "<Your AWS Access Key ID>",
      "secret": "<Your AWS Secret Access Key ID>"
    },
    "bucket": "my-backup-bucket",
    "file_prefix": "my_backups"
  },
  "filename": "my_laptop_backup",
  "directories": [
    "/Users/exampleUser/Desktop/Logos",
    "/var/www/html",
    "/Users/exampleUser/Documents"
  ]
}
```

```json
{
  "aws": {
    "credentials": {
      "key": "<Your AWS Access Key ID>",
      "secret": "<Your AWS Secret Access Key ID>"
    },
    "bucket": "my-bucket",
    "file_prefix": "my_backups"
  },
  "filename": "my_laptop_backup",
  "directories": []
}
```

## Usage

If you don't setup any directories in the config file you can run backup
any folder you want by running the following:

```
s3backup backup path/to/file/or/directory
```

If you have predefined directories in your config file you can simply
run:

```
s3backup backup
```

You can also set an environment variable to only have to run `s3backup`
like below:

```
export S3_BACKUP_SINGLE_COMMAND=1
s3backup
```
