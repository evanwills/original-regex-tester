JS RegEx Tester
===============

JS RegEx Tester is primarily for testing and debugging regular
expressions in JavaScript (or PHP if using functionality not
available in JS). It can also be used for doing sophisticated
 find and replace actions on a given string.

Because it's often hard if not impossible to achieve complex
find/replace actions with a single long, complex and fragile regular
expression JS RegEx Tester allows you to add multiple Find/Replace
pairs to be processed consecutively on the sample.

By default, it uses the XRegExp library (http://xregexp.com) to allow
more powerful regexes. But can use PHP's PCRE regex engine to process
regexes if required.

Eventually, I'd like this to be able to provide a front end for
testing regexes using any web facing language's regex engine
(e.g. Java, Python, .Net, etc) via a JSON API

send JSON: 
{
	"regexTesterRequest": {
		"sample": "sample string",
		"regexes": [
			"regex0": {
				"find": "regex pattern",
				"modifiers": "list of pattern modifiers",
				"replace": "replacement string"
			},
			"regex1": {
				"find": "regex pattern",
				"modifiers": "list of pattern modifiers",
				"replace": "replacement string"
			},
			"regex2": {
				"find": "regex pattern",
				"modifiers": "list of pattern modifiers",
				"replace": "replacement string"
			}
		],
		"delimiter": "delimiter character (if appropriate)",
	}
}

return JSON:
{
	"regexTesterReturn": {
		"sample": "modified sample string"
		"regexes": [
			"regex0": {
				"error": {
					"message": "error message from engine";
					"start": 0 (index of character where the error started)
				}
			},
			"regex1": {
				"succes": [
					{
						"wholeMatch": "the whole match string",
						"captured": [
							"match 1",
							"match 2",
							"etc"
						]
					},
					{
						"wholeMatch": "the whole match string",
						"captured": [
							"match 1",
							"match 2",
							"etc"
						]
					},
					etc
				]
			},
			"regex2": {
				"succes": [
					{
						"wholeMatch": "the whole match string",
						"captured": [
							"match 1",
							"match 2",
							"etc"
						]
					},
					{
						"wholeMatch": "the whole match string",
						"captured": [
							"match 1",
							"match 2",
							"etc"
						]
					},
					etc,
					etc,
					etc
				]
			}
		]
	}
}

# original-regex-tester
