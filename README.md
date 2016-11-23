Symfony Console Command Chaining
=============

Symfony project includes bundle EugeneC\CommandChainBundle that implements command chaining functionality. BarBundle, FooBundle and Foo2Bundle
registers their console commands to be members of a command chain. When a user runs the main command in a chain,
all other commands registered in this chain should be executed as well. Commands registered as chain members
can no longer be executed on their own.

BarBundle, FooBundle and Foo2Bundle provided for demonstrate the work of this application.

Configuration example
=============
    eugene_c_command_chain:
        chains:
            chain_1:
                main: bar:hi
                child: foo:hello
                parameters:
                    arg1:
                        name: firstName
                        value: Foo
                    opt1:
                        name: "--secondName"
                        value: "foo Command from chain 1"
            chain_2:
                main: foo2:hello
                child: bar:hi
            chain_3:
                main: bar:hi
                child: foo:hello
                parameters:
                    arg1:
                        name: firstName
                        value: Foo
                    opt1:
                        name: "--secondName"
                        value: "foo Command from chain 3"