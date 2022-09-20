# Symfony Deployer Bundle - Easy deployment scripting

This bundle can be used to configure scripts that should be run during your deployment.

# Generate new script

Run `bin/console drenso:deployer:generate`.

# Deployment setup

Just add the following commands to your deployment script, at the required positions:

- Pre-deployment scripts: `bin/console drenso:deployer:pre`
- Post-deployment scripts: `bin/console drenso:deployer:post`

# Command types

This bundle distinguishes between two main command types: `always` and `once`. Both can be configured for either the `pre` or `post` deployment hook.
