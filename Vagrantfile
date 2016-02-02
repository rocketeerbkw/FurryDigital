# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "phalcon-vm"
  config.ssh.insert_key = false

  # Support for Parallels provider for Vagrant
  # See: http://parallels.github.io/vagrant-parallels/docs/
  config.vm.provider "parallels" do |v, override|
    # v.update_guest_tools = true
    v.memory = 1024

    override.vm.box = "parallels/ubuntu-14.04"
  end

  # Customization for Virtualbox (default provider)
  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm", :id, "--memory", "1024"]

    # Comment the bottom two lines to disable muli-core in the VM
    vb.customize ["modifyvm", :id, "--cpus", "2"]
    vb.customize ["modifyvm", :id, "--ioapic", "on"]
  end

  if Vagrant.has_plugin?("hostsupdater")
    config.hostsupdater.aliases = ["local.floof.club"]
  end

  # Disabled for Windows 10 + VirtualBox
  # config.vm.network "private_network", ip: "192.168.33.120"
  config.vm.network :forwarded_port, guest: 80, host: 8080

  config.vm.synced_folder ".", "/vagrant/"
  config.vm.synced_folder ".", "/var/www/vagrant/", :owner => 'www-data', :group => 'www-data'

  config.vm.provision "shell" do |s|
    s.path = "util/vagrant_init.sh"
  end

end
