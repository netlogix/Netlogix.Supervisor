Netlogix.Supervisor
===================

This package creates supervisor configuration files via flow CLI.

Configuration files are named "group-something.conf" when they contain a 
supervisor group configuration or "program-something.conf" when they contain 
a supervisor program configuration. Both are created at 
Configuration/Supervisor within your application.


Setup:
------

Supervisor allows to "include" other files and even allows for globbing.

```conf
# cat /etc/supervisor/supervisord.conf
[include]
files=/etc/supervisor/conf.d/*.conf /var/www/*/Configuration/Supervisor/group.conf /var/www/*/Configuration/Supervisor/program*.conf
```

Creating supervisor files dynamically is intended to be a compile time step, 
preferably on deployment.


Available configuration options:
--------------------------------

Program settings get passed to the supervisor configuration file without 
validation, so every current and future configuration option is available.

See: http://supervisord.org/configuration.html#program-x-section-settings


Create supervisor settings via YAML:
------------------------------------

Although supervisor programs can be created entirely via yaml, those are
basically static because the yaml configuration format does not provide
iteration or dynamic construction mechanisms.

```yaml
Netlogix:
    Supervisor:

        programs:

            changes-command-controller-polling-action:

                command: './flow changes:poll ; sleep 5'
                name: 'this-is-a-program'
                environment: "FLOW_CONTEXT='%env:FLOW_CONTEXT%'"
                directory: "%FLOW_PATH_ROOT%"
```

For more details: See Configuration/Settings.yaml.example


Create supervisor settings via PHP:
-----------------------------------

There is a `Provider` interface which allows other packages to hook an and
create Supervisor `Program` settings in a completely programmatic manner.

```php
class Provider implements \Netlogix\Supervisor\Provider
{
    /**
     * @return array<\Netlogix\Supervisor\Model\Program>
     */
    public function getPrograms(): array
    {
        $name = 'this-is-a-program';
        $groupName = 'default';
        $command = './flow changes:poll ; sleep 5';
        $programSettings = [
            'environment' =>  \sprintf(
                "FLOW_CONTEXT='%s'",
                Bootstrap::getEnvironmentConfigurationSetting('FLOW_CONTEXT') ?: 'Development'
            ),
            'directory' => \FLOW_PATH_ROOT
        ];
        return [
            new \Netlogix\Supervisor\Model\Program(
                $name,
                $groupName,
                $command,
                $programSettings
            );
        ];
    }
}
```


Configure group settings:
-------------------------

Groups are created on demand as soon as a program assigns itself to the 
group. This means groups don't need to be configured beforehand or even at all.

If no further configuration is presented, the supervisor group name is 
generated from the "groupName" configuration option of a program.

Groups can override their names. This mens several programs can e.g. refer 
to the same group "default" but the group can be renamed if different 
applications share a common supervisor daemon.

This setting also provides a priority setting.

```yaml
Netlogix:
  Supervisor:

    groups:
      default:
        name: "%FLOW_PATH_ROOT%"
        priority: 200
```


Renamed identifiers:
--------------------

Both, group names and program names need to be cleaned to comply with 
supervisors naming scheme. This is done automatically, so even if the
group name is configured as `%FLOW_PATH_ROOT%` it will be transformed to 
`var-www-document-root`.
