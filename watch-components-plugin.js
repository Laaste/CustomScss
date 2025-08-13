class WatchComponentsPlugin
{
	constructor(files)
	{
		this.files = files;
	}

	apply(compiler)
	{
		compiler.hooks.thisCompilation.tap('WatchComponentsPlugin', (compilation) =>
		{
			compilation.hooks.additionalAssets.tap('WatchComponentsPlugin', () =>
			{
				this.files.forEach(file =>
				{
					compilation.fileDependencies.add(file);
				});
			});
		});
	}
}

module.exports = WatchComponentsPlugin;