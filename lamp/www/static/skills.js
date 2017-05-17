$(document).ready(function(){
	if($('#skillsArray').length) {
		skillsArray = document.getElementById('skillsArray').innerHTML;
		skillsArray = skillsArray.split(",");
		j = 0;
		while (j < skillsArray.length) {
			//For some reason this checks if it's not NaN
			if (Number(skillsArray[j])) {

				//Intelligence Percentage + Title
				//document.getElementById('contentContainer').innerHTML += "<div class='intelligenceTitle'>" + skillsArray[j+1] + " " + 100*Number(skillsArray[j]) + "%</div>";
				document.getElementById('contentContainer').innerHTML += "<div class='intelligenceTitle'>" + 100*Number(skillsArray[j]) + "% " + skillsArray[j+1] + "</div>";
				
				//Definitions + Tasks
				var taskString = "";
				taskString += "<div class='intelligenceDefinition'><ul><li>";

				switch(skillsArray[j+1]) {

				case "Naturalistic Intelligence":
					taskString += "Designates the human ability to discriminate among living things (plants, animals) as well as sensitivity to other features of the natural world (clouds, rock configurations).  This ability was clearly of value in our evolutionary past as hunters, gatherers, and farmers; it continues to be central in such roles as botanist or chef.  It is also speculated that much of our consumer society exploits the naturalist intelligences, which can be mobilized in the discrimination among cars, sneakers, kinds of makeup, and the like.";
					break;
				case "Musical Intelligence":
					taskString += "Musical intelligence is the capacity to discern pitch, rhythm, timbre, and tone.  This intelligence enables us to recognize, create, reproduce, and reflect on music, as demonstrated by composers, conductors, musicians, vocalist, and sensitive listeners.  Interestingly, there is often an affective connection between music and the emotions; and mathematical and musical intelligences may share common thinking processes.  Young adults with this kind of intelligence are usually singing or drumming to themselves.  They are usually quite aware of sounds others may miss.";
					break;
				case "Logical-Mathematical Intelligence":
					taskString += "Logical-mathematical intelligence is the ability to calculate, quantify, consider propositions and hypotheses, and carry out complete mathematical operations.  It enables us to perceive relationships and connections and to use abstract, symbolic thought; sequential reasoning skills; and inductive and deductive thinking patterns.  Logical intelligence is usually well developed in mathematicians, scientists, and detectives.  Young adults with lots of logical intelligence are interested in patterns, categories, and relationships.  They are drawn to arithmetic problems, strategy games and experiments.";
					break;
				case "Existential Intelligence":
					taskString += "Sensitivity and capacity to tackle deep questions about human existence, such as the meaning of life, why do we die, and how did we get here.";
					break;
				case "Interpersonal Intelligence":
					taskString += "Interpersonal intelligence is the ability to understand and interact effectively with others.  It involves effective verbal and nonverbal communication, the ability to note distinctions among others, sensitivity to the moods and temperaments of others, and the ability to entertain multiple perspectives.  Teachers, social workers, actors, and politicians all exhibit interpersonal intelligence.  Young adults with this kind of intelligence are leaders among their peers, are good at communicating, and seem to understand others' feelings and motives.";
					break;
				case "Bodily-Kinesthetic Intelligence":
					taskString += "Bodily kinesthetic intelligence is the capacity to manipulate objects and use a variety of physical skills.  This intelligence also involves a sense of timing and the perfection of skills through mind–body union.  Athletes, dancers, surgeons, and craftspeople exhibit well-developed bodily kinesthetic intelligence.";
					break;
				case "Linguistic Intelligence":
					taskString += "Linguistic intelligence is the ability to think in words and to use language to express and appreciate complex meanings.  Linguistic intelligence allows us to understand the order and meaning of words and to apply meta-linguistic skills to reflect on our use of language.  Linguistic intelligence is the most widely shared human competence and is evident in poets, novelists, journalists, and effective public speakers.  Young adults with this kind of intelligence enjoy writing, reading, telling stories or doing crossword puzzles.";
					break;
				case "Intra-personal Intelligence":
					taskString += "Intra-personal intelligence is the capacity to understand oneself and one's thoughts and feelings, and to use such knowledge in planning and directioning one's life.  Intra-personal intelligence involves not only an appreciation of the self, but also of the human condition.  It is evident in psychologist, spiritual leaders, and philosophers.  These young adults may be shy.  They are very aware of their own feelings and are self-motivated.";
					break;
				case "Spatial Intelligence":
					taskString += "Spatial intelligence is the ability to think in three dimensions.  Core capacities include mental imagery, spatial reasoning, image manipulation, graphic and artistic skills, and an active imagination.  Sailors, pilots, sculptors, painters, and architects all exhibit spatial intelligence.  Young adults with this kind of intelligence may be fascinated with mazes or jigsaw puzzles, or spend free time drawing or daydreaming.";
					break;
				default:
				}

				taskString += "</li></ul></div>";

				//Intelligence Tasks
				taskString += "<div class='intelligenceTasks'><ul>";

				j += 2;
				counter = 1;
				while (j < skillsArray.length && skillsArray[j][0] == counter.toString()) {
					
					taskString += "<li>" + skillsArray[j].substring(3);
					j++;

					while (j < skillsArray.length) {
						if ((skillsArray[j][0] == (counter+1).toString() && skillsArray[j][1] == '.' && skillsArray[j][2] == ' ') || Number(skillsArray[j])) {
							break;
						}
						taskString += "," + skillsArray[j];
						j++;
					}


					taskString += "</li>";
					counter++;

				}

				taskString += "</ul></div>";
				document.getElementById('contentContainer').innerHTML += taskString;

				


			}
		}
	}
});




