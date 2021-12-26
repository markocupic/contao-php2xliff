<p align="center"><a href="https://github.com/markocupic"><img src="docs/logo.png" width="200"></a></p>

# Generate XLIFF translation files from PHP array translation files
This little handy Contao backend extension simply converts contao php translation files into their xliff (*.xlf) pendants.
 The newly generated files will be stored side by side to the already existing php files.
 Already existing *.xlf files will be overwritten.

![Backend](docs/screenshot_backend_2.png)
![Backend](docs/screenshot_backend_1.png)

# Changing the source language
The source language for all Contao Core XLIFF files is english (en).
 If you like to change this setting you have to do that in your project config in `config/config.yml`.

```yaml
# config/config.yml
markocupic_contao_php2xliff:
  sourceLanguage: it # Switch the source language to Italian
```

#Transifex hints
Navigate to the repsitory root `cd vendor/vendorname/bundlename`
Init transifex `tx init`. Transifex will create a config file in `vendor/vendorname/bundlename/.tx`
Edit the config file and add entries for each translation file.

Use `markocupic/contao-php2xliff` to generate xliff translation files from Contao PHP array translation files.

### Push

Upload/Update source files: `tx push -s`

Upload/Update translations files: `tx push -t`

Or upload/update both: `tx push -s -t`

Upload/Update translations files and **force upload**: `tx push -t -f`

[Transifex Push Documentation](https://docs.transifex.com/client/push)

### Pull
Pull source files (force overriding): `tx pull -s -f`

Pull translation files (force overriding): `tx pull -t -f`

[Transifex Pull Documentation](https://docs.transifex.com/client/pull)
