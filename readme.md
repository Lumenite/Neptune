# Neptune Kubernetes PHP Framework

## Kubernetes deployment made easy for any project

> Note: Neptune is in development stage. Commands or APIs may change when a better solution is found.

### Prerequisites:
1. Kubernetes cluster should be up and running.
2. Storage class should be present before running `neptune release:create` command.

### Quick setup:

* As this package is in development stage, it is require to add below lines to `~/.composer/composer.json` to allow composer to download Neptune dev version 
```
{
    "minimum-stability": "dev",
    "prefer-stable": true,
}
```
* Globally require Neptune and make sure `export PATH=~/.composer/vendor/bin:$PATH` exists in your `~/.bashrc` or `~/.profile` or `~/.zshrc` file.
```
composer global require lumenite/neptune
```

* Now you should see below screen after running `neptune` command

<img width="786" alt="Screenshot 2020-01-25 at 01 40 33" src="https://user-images.githubusercontent.com/7669734/73121298-fb1f9d80-3f78-11ea-8545-0f525b1f8bd4.png">

### Neptune Terminology:
- Release are the sets of file which contain kubernetes resources.
- After running `neptune release:build` command you should see `kubernetes/{your-app-name}` folder in your root directory.

<img width="150" alt="Neptune Release Directory" src="https://user-images.githubusercontent.com/7669734/73121485-e0e6bf00-3f7a-11ea-825f-f8b3ccf4f440.png" align="center" />

- `app.yml` is `deployment` resource.
- `artifact` is `job` resource.
- `disk.yml` is `pvc` PersistentVolumeClaim resource.
- `values.yml` is the placeholder files which will replace all the variables inside the resource files.
- Another temporary directory is generated to keep track on recent files which are deployed. You can find them in `storages/k8s` folder

<img width="180" alt="Neptune Storage Directory" src="https://user-images.githubusercontent.com/7669734/73121602-fd372b80-3f7b-11ea-983d-b83c0f349385.png">

### License

The Neptune framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


<a name="Contribution"></a>
### Contribution:
1. Give it a star if you like this package.
2. Help to keep readme up to date with some functionality which exist in this framework but not visible to others.
3. Feel free to contribute or any suggestion which can help PHP and Kubernetes community to grow.
4. Just do it. You are welcome :)

### Credits

| Contributors           | Twitter   | Ask for Help | Site |
|------------------------|-----------|--------------|------|
| [Mohammed Mudassir](https://github.com/Modelizer) (Creator) | @[md_mudasir](https://twitter.com/md_mudasir) | hello@mudasir.me | [http://mudasir.me](http://mudasir.me/) |
